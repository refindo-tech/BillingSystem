<?php

namespace App\Support;

use App\Enum\PlanType;
use App\Enum\RechargeGateway;
use App\Enum\ValidityUnit;
use App\Enum\ValidityCycle;
use App\Enum\PaymentGatewayStatus;
use App\Models\PaymentGateway;
use App\Exceptions\PackageRechargeException;
use App\Models\Customer;
use App\Models\Plan;
use App\Models\Router;
use App\Models\Transaction;
use App\Models\UserRecharge;
use App\Models\PendingUserRecharge;

use App\Support\Facades\Config;
use App\Support\Facades\Xendit;
use App\Support\Facades\Tripay;

use Illuminate\Support\Facades\DB;

class Package
{

public static function rechargeUser(
    Customer $customer,
    Router $mikrotik,
    Plan $plan,
    RechargeGateway $gateway,
    string $channel,
    string $serviceNumber = null,
    $validityCycle = null,
    $expiredAt = null,
    $username = null,
    $pppoePassword = null,
    $server_id = null
) {
    return DB::transaction(function () use (
        $customer, $mikrotik, $plan, $gateway, $channel,
        $serviceNumber, $validityCycle, $expiredAt, $username,
        $pppoePassword, $server_id
    ) {
        $date_now = now();
        $serviceNumber = $serviceNumber ?? static::generateServiceNumber($customer);

        $userRecharge = UserRecharge::where([
            'customer_id' => $customer->id,
            'router_id' => $mikrotik->id,
        ])->first();

        $date_exp = $expiredAt ?? static::calculateExpiration($plan, $validityCycle, $userRecharge);

        $userRecharge = static::createUserRecharge(
            $customer,
            $mikrotik,
            $plan,
            $gateway,
            $channel,
            $serviceNumber,
            $date_now,
            $date_exp,
            $validityCycle,
            $username,
            $pppoePassword,
            $server_id
        );

        

        // Jika transaksi gagal, rollback otomatis akan terjadi
        if (!static::createUserTransaction($userRecharge, $channel)) {
            throw new \Exception('Failed to create user transaction');
        }

        return $userRecharge;
    });
}

    
    /**
     * Handles the creation or update of the UserRecharge and Transaction records.
     */
    private static function createUserRecharge(
        Customer $customer,
        Router $mikrotik,
        Plan $plan,
        RechargeGateway $gateway,
        string $channel,
        string $serviceNumber,
        $date_now,
        $date_exp,
        $validityCycle,
        $username,
        $pppoePassword,
        $server_id
    ) {

        // $userRecharge = UserRecharge::where([
        //     'customer_id' => $customer->id,
        //     'router_id' => $mikrotik->id,
        // ])->first();

        $userRecharge = null;

        // dd($username, $pppoePassword, $plan->type, $plan->name, $date_now, $date_exp, $gateway->value, $channel, $mikrotik->id, $serviceNumber, $validityCycle, $server_id);
    
        if ($userRecharge) {
            // Extend validity if same plan is active
            if ($userRecharge->namebp == $plan->name && $userRecharge->is_active) {
                $date_exp = static::extendExpiration($userRecharge, $plan, $validityCycle);
            }
    
            $userRecharge->update([
                'recharged_at' => $date_now,
                'expired_at' => $date_exp,
                'status' => 'off',
                'method' => "$gateway->value - $channel",
                'plan_id' => $plan->id,
                'namebp' => $plan->name,
            ]);
        } else {
            $userRecharge = UserRecharge::create([
                'customer_id' => $customer->id,
                'username' => $username,
                'pppoe_password' => $pppoePassword,
                'plan_id' => $plan->id,
                'initial_plan_id' => $plan->id,
                'namebp' => $plan->name,
                'recharged_at' => $date_now,
                'expired_at' => $date_exp,
                'status' => 'off',
                'method' => "$gateway->value - $channel",
                'router_id' => $mikrotik->id,
                'type' => $plan->type,
                'service_number' => $serviceNumber,
                'validity_cycle' => $validityCycle,
                'server_id' => $server_id,
            ]);
        }

        //create user transaction
        // static::createUserTransaction($userRecharge);
        return $userRecharge;
    }

    //create user transaction
    public static function createUserTransaction(UserRecharge $userRecharge, $channel)
    {
        $activeGateway = Config::get('active_payment_gateway');
        if (empty($activeGateway)) {
            $activeGateway = 'tripay';
        }
        
        // Validate selected payment gateway config
        if ($activeGateway === 'xendit') {
            Xendit::validateConfig();
        } elseif ($activeGateway === 'tripay') {
            Tripay::validateConfig();
        } else {
            return redirect()->back()->with('error', 'Invalid payment gateway configuration.');
        }

        

        $order = PaymentGateway::where('user_recharge_id', $userRecharge->id)
            ->where('status', PaymentGatewayStatus::UNPAID)
            ->first();

        // Check for existing unpaid transaction
        if ($order && $order->pg_url_payment) {
            throw new PackageRechargeException('There is an existing unpaid transaction for ' . $userRecharge->customer->fullname);
        }

        

        if (empty($order)) {
            $order = PaymentGateway::create([
                'user_recharge_id' => $userRecharge->id,
                'username' => $userRecharge->customer->username,
                'gateway' => $activeGateway,
                'plan_id' => $userRecharge->plan_id,
                'plan_name' => $userRecharge->plan->name,
                'router_id' => $userRecharge->router_id,
                'router_name' => $userRecharge->router->name,
                'price' => $userRecharge->plan->price,
                'payment_channel' => $channel,
                'status' => PaymentGatewayStatus::UNPAID,
                'transaction_type' => 'new',
            ]);
        } else {
            $order->update([
                'username' => $userRecharge->customer->username,
                'gateway' => $activeGateway,
                'plan_id' => $userRecharge->plan_id,
                'plan_name' => $userRecharge->plan->name,
                'router_id' => $userRecharge->router_id,
                'router_name' => $userRecharge->router->name,
                'price' => $userRecharge->plan->price,
                'status' => PaymentGatewayStatus::UNPAID,
            ]);
        }

        static::createInvoice($userRecharge, $activeGateway, $channel);

        return $activeGateway === 'xendit'
            ? Xendit::createTransaction($order, $userRecharge->customer)
            : Tripay::createTransaction($order, $userRecharge->customer);
    }

    private static function createMikrotikAccount(UserRecharge $userRecharge)
    {
        $client = static::resetCustomerMikrotik($userRecharge->router, $userRecharge->customer);
        if ($userRecharge->plan->type == PlanType::HOTSPOT) {
            if ($userRecharge->plan->is_radius) {
                //TODO:
            } else {
                Mikrotik::addHotspotUser($client, $userRecharge->plan, $userRecharge->customer, $userRecharge->username, $userRecharge->pppoe_password);
            }
        } else {
            if ($userRecharge->plan->is_radius) {
                //TODO:
            } else {
                Mikrotik::addPpoeUser($client, $userRecharge->plan, $userRecharge->customer, $userRecharge->username, $userRecharge->pppoe_password);
            }
        }
    }

    //Creat invoice
    public static function activatePackage(UserRecharge $userRecharge, $rechargeGateway, $channel, $trasaction_type)
    {
        static::createMikrotikAccount($userRecharge);
        //if transaction type is recharge, update the expiration date
        $expiredAt = ($trasaction_type == 'recharge')
            ? static::calculateExpiration($userRecharge->plan, $userRecharge->validity_cycle, $userRecharge)
            : $userRecharge->expired_at;

            $userRecharge->update([
                'status' => 'on',
                'expired_at' => $expiredAt,
            ]);

        
        return true;
    }

    public static function createInvoice(UserRecharge $userRecharge, $rechargeGateway, $channel)
    {
        $pendingUserRecharge = PendingUserRecharge::where('user_recharge_id', $userRecharge->id)->first();
        $price = ($pendingUserRecharge && $pendingUserRecharge->price !== null)
            ? $pendingUserRecharge->price
            : $userRecharge->plan->price;

        Transaction::create([
            'invoice' => 'INV-' . Package::_raid(5),
            'username' => $userRecharge->customer->username,
            'plan_name' => $userRecharge->plan->name,
            'price' => $price,
            'recharged_at' => $userRecharge->recharged_at,
            'expired_at' => $userRecharge->expired_at,
            'method' => "$rechargeGateway - $channel",
            'routers' => $userRecharge->router->name,
            'type' => $userRecharge->plan->type,
        ]);

        return true;
    }
    
    /**
     * Generates a unique service number.
     */
    public static function generateServiceNumber(Customer $customer)
    {
        $prefix = '00000';
        $serviceNumber = date('y');
        $serviceNumber .= substr($prefix, 0, max(0, strlen($prefix) - strlen($customer->id))) . $customer->id;
        $prefix2 = '00';
        $serviceCount = UserRecharge::where('customer_id', $customer->id)->count() + 1;
        $serviceNumber .= substr($prefix2, 0, max(0, strlen($prefix2) - strlen($serviceCount))) . $serviceCount;
    
        return $serviceNumber;
    }
    
    /**
     * Calculates the expiration date.
     */
    public static function calculateExpiration(Plan $plan, $validityCycle, $userRecharge)
    {
        $expiredAt = $userRecharge->expired_at->isPast() ? now() : $userRecharge->expired_at;
        return match ($validityCycle) {
            ValidityCycle::FIXED, ValidityCycle::MONTHLY => date('Y-m-d H:i:s', strtotime($expiredAt. ' + 1 month')),
            default => match ($plan->validity_unit) {
            ValidityUnit::MONTHS => $expiredAt->addMonths($plan->validity),
            ValidityUnit::DAYS => $expiredAt->addDays($plan->validity),
            ValidityUnit::HRS => $expiredAt->addHours($plan->validity),
            ValidityUnit::MINS => $expiredAt->addMinutes($plan->validity),
            default => throw new PackageRechargeException('Invalid validity unit')
            }
        };
    }
    
    /**
     * Extends the expiration date if the user has an active plan.
     */
    private static function extendExpiration(UserRecharge $userRecharge, Plan $plan, $validityCycle)
    {
        return match ($validityCycle) {
            ValidityCycle::FIXED, ValidityCycle::MONTHLY => date('Y-m-d H:i:s', strtotime($userRecharge->expired_at. ' + 1 month')),
            default => static::calculateExpiration($plan, $validityCycle, $userRecharge),
        };
    }
    

    public static function changeTo(Customer $customer, Plan $plan, UserRecharge $userRecharge, String $username, String $pppoePassword)
    {
        /** @var Router $mikrotik */
        $mikrotik = $userRecharge->router;
        if ($plan->router->id != $userRecharge->router_id && !$plan->is_radius) {
            $mikrotik = $plan->router;
        }
        $client = static::resetCustomerMikrotik($mikrotik, $customer);
        if ($plan->type == PlanType::HOTSPOT) {
            if ($plan->is_radius) {
                //TODO:
            } else {
                Mikrotik::addHotspotUser($client, $plan, $customer, $username, $pppoePassword);
            }
        } else {
            if ($plan->is_radius) {
                //TODO:
            } else {
                Mikrotik::addPpoeUser($client, $plan, $customer, $username, $pppoePassword);
            }
        }


        $price = ($plan->id == $userRecharge->plan_id)
            ? $plan->price
            : static::calculatePrice($userRecharge->recharged_at, $userRecharge->expired_at, $userRecharge->plan->price, $plan->price);

        //check if there is a pending transaction
        $pendingPayment = PaymentGateway::where('user_recharge_id', $userRecharge->id)->where('status', PaymentGatewayStatus::UNPAID)->first();

        if ($pendingPayment) {
            throw new PackageRechargeException('There is an existing unpaid transaction for ' . $userRecharge->customer->fullname);
        }

        //check if there is a pending transaction
        $pending = PendingUserRecharge::where('user_recharge_id', $userRecharge->id)->where('status', 'waiting')->first();
        $scheduledFor = $userRecharge->expired_at->subDays(7);
        if ($pending) {
            $pending->update([
                'customer_id' => $customer->id,
                'plan_id' => $plan->id,
                'router_id' => $mikrotik->id,
                'server_id' => $userRecharge->server_id,
                'username' => $username,
                'price' => $price,
                'status' => 'waiting',
                'scheduled_for' => $scheduledFor,
            ]);
        } else {
            PendingUserRecharge::create([
                'user_recharge_id' => $userRecharge->id,
                'customer_id' => $customer->id,
                'plan_id' => $plan->id,
                'router_id' => $mikrotik->id,
                'server_id' => $userRecharge->server_id,
                'username' => $username,
                'price' => $price,
                'status' => 'waiting',
                'scheduled_for' => $scheduledFor,
            ]);
        }
    }

    public static function calculatePrice($startDateTime, $endDateTime, $oldPlanPrice, $newPlanPrice)
    {
        $start = strtotime($startDateTime);
        $end = strtotime($endDateTime);
        $today = time();
        $totalDays = 30;
        $usedDays = ($today - $start) / (60 * 60 * 24);
        $remainingDays = ($end - $today) / (60 * 60 * 24);

        $oldPlanPricePerDay = $oldPlanPrice / $totalDays;
        $newPlanPricePerDay = $newPlanPrice / $totalDays;
        $totalPrice = ($oldPlanPricePerDay * $usedDays) + ($newPlanPricePerDay * $remainingDays);
        return round($totalPrice / 500) * 500;
        
    }

    public static function _raid($l)
    {
        return substr(str_shuffle(str_repeat('0123456789', $l)), 0, $l);
    }

    private static function resetCustomerMikrotik(Router $mikrotik, Customer $customer)
    {
        $client = Mikrotik::getClient($mikrotik->ip_address, $mikrotik->username, $mikrotik->password);
        Mikrotik::removeHotspotUser($client, $customer->username);
        Mikrotik::removePpoeUser($client, $customer->username);
        Mikrotik::removeHotspotActiveUser($client, $customer->username);
        Mikrotik::removePpoeActive($client, $customer->username);

        return $client;
    }
}
