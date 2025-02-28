<?php

namespace App\Support;

use App\Enum\PlanType;
use App\Enum\RechargeGateway;
use App\Enum\ValidityUnit;
use App\Enum\ValidityCycle;
use App\Exceptions\PackageRechargeException;
use App\Models\Customer;
use App\Models\Plan;
use App\Models\Router;
use App\Models\Transaction;
use App\Models\UserRecharge;

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
        String $username = null,
        String $pppoePassword = null,
        $server_id = null
    ) {
        $date_now = now();
        $serviceNumber = $serviceNumber ?? static::generateServiceNumber($customer);
        
        $userRecharge = UserRecharge::where([
            'customer_id' => $customer->id,
            'router_id' => $mikrotik->id,
        ])->first();
    
        $date_exp = $expiredAt ?? static::calculateExpiration($plan, $validityCycle, $userRecharge);
    
        if (!$plan->is_radius) {
            static::createMikrotikAccount($mikrotik, $customer, $plan, $username, $pppoePassword);
        } else {
            // TODO: Handle radius integration
        }
    
        return static::createUserRecharge(
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
        $userRecharge = UserRecharge::where([
            'customer_id' => $customer->id,
            'router_id' => $mikrotik->id,
        ])->first();
    
        if ($userRecharge) {
            // Extend validity if same plan is active
            if ($userRecharge->namebp == $plan->name && $userRecharge->is_active) {
                $date_exp = static::extendExpiration($userRecharge, $plan, $validityCycle);
            }
    
            $userRecharge->update([
                'recharged_at' => $date_now,
                'expired_at' => $date_exp,
                'status' => 'on',
                'method' => "$gateway->value - $channel",
                'plan_id' => $plan->id,
                'namebp' => $plan->name,
            ]);
        } else {
            UserRecharge::create([
                'customer_id' => $customer->id,
                'username' => $username ?? $customer->username,
                'pppoe_password' => $pppoePassword,
                'plan_id' => $plan->id,
                'namebp' => $plan->name,
                'recharged_at' => $date_now,
                'expired_at' => $date_exp,
                'status' => 'on',
                'method' => "$gateway->value - $channel",
                'router_id' => $mikrotik->id,
                'type' => $plan->type,
                'service_number' => $serviceNumber,
                'validity_cycle' => $validityCycle,
                'server_id' => $server_id,
            ]);
        }
    
        // Transaction::create([
        //     'invoice' => 'INV-' . Package::_raid(5),
        //     'username' => $customer->username,
        //     'plan_name' => $plan->name,
        //     'price' => $plan->price,
        //     'recharged_at' => $date_now,
        //     'expired_at' => $date_exp,
        //     'method' => "$gateway->value - $channel",
        //     'routers' => $mikrotik->name,
        //     'type' => $plan->type,
        // ]);
    
        return true;
    }

    //create transaction

    
    /**
     * Creates a new MikroTik account (Hotspot or PPPoE).
     */
    private static function createMikrotikAccount(
        Router $mikrotik,
        Customer $customer,
        Plan $plan,
        $username,
        $pppoePassword
    ) {
        $client = static::resetCustomerMikrotik($mikrotik, $customer);
    
        if ($plan->type == PlanType::HOTSPOT) {
            Mikrotik::addHotspotUser($client, $plan, $customer, $username, $pppoePassword);
        } else {
            Mikrotik::addPpoeUser($client, $plan, $customer, $username, $pppoePassword);
        }
    }
    
    /**
     * Generates a unique service number.
     */
    private static function generateServiceNumber(Customer $customer)
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
    private static function calculateExpiration(Plan $plan, $validityCycle, $userRecharge)
    {
        return match ($plan->validity_unit) {
            ValidityUnit::MONTHS => now()->addMonths($plan->validity),
            ValidityUnit::DAYS => now()->addDays($plan->validity),
            ValidityUnit::HRS => now()->addHours($plan->validity),
            ValidityUnit::MINS => now()->addMinutes($plan->validity),
            default => throw new PackageRechargeException('Invalid validity unit')
        };
    }
    
    /**
     * Extends the expiration date if the user has an active plan.
     */
    private static function extendExpiration(UserRecharge $userRecharge, Plan $plan, $validityCycle)
    {
        return match ($validityCycle) {
            ValidityCycle::FIXED, ValidityCycle::MONTHLY => $userRecharge->expired_at->addMonth(),
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
