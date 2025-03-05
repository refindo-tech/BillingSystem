<?php

namespace App\Http\Controllers\Customer;

use App\DataTables\OrderHistoryDataTable;
use App\Enum\PaymentGatewayStatus;
use App\Enum\PendingUserRechargeStatus;
use App\Enum\ValidityCycle;
use App\Enum\ValidityUnit;
use App\Enum\RechargeGateway;
use App\Exceptions\AppException;
use App\Http\Controllers\Controller;
use App\Models\PaymentGateway;
use App\Models\PendingUserRecharge;
use App\Models\Plan;
use App\Models\Router;
use App\Support\Facades\Config;
use App\Support\Facades\Xendit;
use App\Support\Facades\Tripay;
use App\Models\Customer;

use App\Support\Package;


use Illuminate\Support\Collection;

class CustomerOrderController extends Controller
{
    public function index()
    {
        $routers = Router::whereEnabled(true)->get();

        $activeGateway = Config::get('active_payment_gateway');
        $channelsConfig = config("payment.{$activeGateway}.channels");
        $paymentChannels = explode(',', Config::get("{$activeGateway}_channels"));

        if (empty($paymentChannels[0])) {
            $activeChannels = collect($channelsConfig)->mapWithKeys(function ($channel) {
                return [$channel['id'] => $channel['name']];
            })->toArray();
        } else {
            $activeChannels = collect($paymentChannels)->mapWithKeys(function ($channel) use ($channelsConfig) {
                $channelConfig = collect($channelsConfig)->firstWhere('id', $channel);
                return [$channel => $channelConfig['name']];
            })->toArray();
        }

        return view('customer.order.list', compact('routers', 'activeChannels'));
    }

    public function buy(Plan $plan)
    {
        $user = auth()->user();

        // Cek apakah ada transaksi yang belum dibayar
        $order = PaymentGateway::where('username', $user->username)
            ->where('status', PaymentGatewayStatus::UNPAID)
            ->first();

        if ($order && $order->pg_url_payment) {
            return redirect()->route('customer:order.detail', $order)->with('error', 'You already have an unpaid transaction. Please cancel or pay it.');
        }

        // Ambil gateway aktif dan payment channel dari request
        $rechargeGateway = Config::get('active_payment_gateway', 'tripay');
        $selectedChannel = request()->get('gateway');

        // Ambil daftar channel yang tersedia dari konfigurasi
        $channelsConfig = config("payment.{$rechargeGateway}.channels");
        $validChannels = collect($channelsConfig)->pluck('id')->toArray();

        // Validasi channel yang dipilih
        if (!in_array($selectedChannel, $validChannels)) {
            return redirect()->back()->with('error', 'Invalid payment channel selected.');
        }

        // Generate service number & authentication credentials
        $serviceNumber = Package::generateServiceNumber($user);
        $username = $serviceNumber . '@netplus.id';
        $password = $user->pppoe_password;
        $dateNow = now();
        $dateExpired = match ($plan->validity_unit) {
            ValidityUnit::MONTHS => $dateNow->copy()->addMonths($plan->validity),
            ValidityUnit::DAYS => $dateNow->copy()->addDays($plan->validity),
            ValidityUnit::HRS => $dateNow->copy()->addHours($plan->validity),
            ValidityUnit::MINS => $dateNow->copy()->addMinutes($plan->validity),
            default => $dateNow,
        };

        // Validity cycle & router settings
        $validityCycle = ValidityCycle::PROFILE;
        $server_id = $plan->router->server_id;
        $router = $plan->router;

        // Recharge user account
        $userRecharge = Package::rechargeUser(
            $user,
            $router,
            $plan,
            RechargeGateway::USER,
            $selectedChannel, // Payment channel dari SweetAlert
            $serviceNumber,
            $validityCycle,
            $dateExpired,
            $username,
            $password,
            $server_id
        );

        
        return redirect()->route('customer:dashboard')->with('success', 'Transaction has been created');
    }


    public function detail(PaymentGateway $order)
    {

        if (empty($order->pg_url_payment)) {
            return redirect()->route('customer:order.buy', $order->plan)->with('error', 'Checking Payment');
        }

        return view('customer.order.detail', compact('order'));
    }

    public function activateBill(PendingUserRecharge $bill)
    {
        $activeGateway = Config::get('active_payment_gateway', 'tripay');
        $payment_channel = explode(' - ', $bill->userRecharge->method)[1] ?? 'default';

        $order = PaymentGateway::create
        ([
                'username' => $bill->customer->username,
                'user_recharge_id' => $bill->userRecharge->id,
                'gateway' => $activeGateway,
                'plan_id' => $bill->plan_id,
                'plan_name' => $bill->plan->name,
                'router_id' => $bill->router_id,
                'router_name' => $bill->router->name,
                'price' => $bill->price,
                'status' => PaymentGatewayStatus::UNPAID,
                'payment_channel' => $payment_channel,
                'transaction_type' => 'recharge',
            ]);

        //process transaction
        if ($activeGateway === 'xendit') {
            Xendit::createTransaction($order, $bill->customer);
        } elseif ($activeGateway === 'tripay') {
            Tripay::createTransaction($order, $bill->customer);
        } else {
            throw new AppException('Invalid payment gateway.');
        }

        $bill->update(['status' => PendingUserRechargeStatus::CONFIRMED]);

        return view('customer.order.detail', compact('order'));
    }

    public function cancelBill(PendingUserRecharge $bill)
    {

        $bill->update(['status' => PendingUserRechargeStatus::CANCELED]);


        return redirect()->back()->with('success', 'Transaction has been canceled');
    }

    public function check(PaymentGateway $order)
    {
        $customer = Customer::find(auth()->id()); // This should be a Customer model instance
        $customer = auth()->user(); // This should be a Customer model instance
        if (!$customer instanceof Customer) {
            throw new AppException('Authenticated user is not a customer');
        }

        try {
            if ($order->gateway === 'xendit') {
                Xendit::validateConfig();
                Xendit::getStatus($order, $customer);

            } elseif ($order->gateway === 'tripay') {
                Tripay::validateConfig();
                Tripay::getStatus($order, $customer);
            } else {
                throw new AppException('Invalid payment gateway.');
            }

            return redirect()->route('customer:order.detail', $order)->with('success', 'Transaction has been paid');
        } catch (AppException $e) {
            return redirect()->route('customer:order.detail', $order)->with('error', $e->getMessage());
        }
    }


    public function cancel(PaymentGateway $order)
    {
        $order->update([
            'status' => PaymentGatewayStatus::CANCELED,
        ]);

        //delete user recharge
        $order->userRecharge()->delete();

        return redirect()->back()->with('success', 'Transaction has been canceled');
    }

    public function history(OrderHistoryDataTable $dataTable)
    {
        return $dataTable->render('customer.order.history');
    }
}
