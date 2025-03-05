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
use App\Jobs\SendWhatsAppMessageJob;
use App\Models\PaymentGateway;
use App\Models\PendingUserRecharge;
use App\Models\Plan;
use App\Models\Router;
use App\Models\Server;
use App\Support\Facades\Config;
use App\Support\Facades\Xendit;
use App\Support\Facades\Tripay;
use App\Models\Customer;
use App\Models\KeyWhatsapp;
use App\Models\WhatsappMessage;
use App\Models\WhatsAppTemplate;
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
        $server_id = Server::where('router_id', $plan->router_id)->first()->id;
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
        $customer = Customer::find(auth()->id()); // Ambil data customer
        if (!$customer instanceof Customer) {
            throw new AppException('Authenticated user is not a customer');
        }

        try {
            // Cek status pembayaran berdasarkan gateway
            if ($order->gateway === 'xendit') {
                Xendit::validateConfig();
                Xendit::getStatus($order, $customer);
                
            } elseif ($order->gateway === 'tripay') {
                Tripay::validateConfig();
                Tripay::getStatus($order, $customer);
            } else {
                throw new AppException('Invalid payment gateway.');
            }

            // Cek apakah status pembayaran sukses
            if ($order->status === PaymentGatewayStatus::PAID) {
                // Generate pesan otomatis
                $message = $this->generatePaymentMessage($order, $customer);

                if (!empty($customer->phonenumber) && $message) {
                    $tokenDevice = KeyWhatsapp::first()->key_device;

                    // Kirim pesan WhatsApp via Job Queue
                    SendWhatsAppMessageJob::dispatch($customer->phonenumber, $message, $tokenDevice);

                    // Simpan log pesan ke database
                    WhatsappMessage::create([
                        'phone'   => $customer->phonenumber,
                        'message' => $message,
                        'date'    => now(),
                        'status'  => 'sent',
                    ]);
                }
            }

            return redirect()->route('customer:order.detail', $order)->with('success', 'Transaction has been paid');
        } catch (AppException $e) {
            return redirect()->route('customer:order.detail', $order)->with('error', $e->getMessage());
        }
    }

    private function generatePaymentMessage(PaymentGateway $order, $customer)
    {
        // Ambil template pesan dari database berdasarkan tipe 'Pembayaran'
        $template = WhatsAppTemplate::where('type', 'invoice')->first();
        if (!$template) return null;

        // Data pengganti untuk template
        $replacements = [
            '#INVOICE#'              => $order->id, // Sesuaikan dengan ID invoice
            '#NOLAYANAN#'            => $order->router_id ?? '-', // Jika ada nomor layanan
            '#NAMAPELANGGAN#'        => $customer->fullname,
            '#CHANNEL#'              => strtoupper($order->payment_channel), // XENDIT, TRIPAY, dll.
            '#TGLBAYAR#'             => $order->paid_date->format('d-m-Y H:i'),
            '#SUBTOTAL#'             => number_format($order->price, 0, ',', '.'),
            '#DISKON#'               => number_format(0, 0, ',', '.'), // Sesuaikan jika ada diskon
            '#KODEUNIK#'             => number_format(0, 0, ',', '.'), // Sesuaikan jika ada kode unik
            '#PPN#'                  => number_format(0, 0, ',', '.'), // Sesuaikan jika ada PPN
            '#ADM#'                  => number_format(0, 0, ',', '.'), // Sesuaikan jika ada biaya admin
            '#TOTAL#'                => number_format($order->price, 0, ',', '.'),
            '#LAYANANAKTIFSAMPAI#'   => optional($order->paid_date)->addMonth()->format('d-m-Y') ?? '-',// Jika layanan aktif 1 bulan
        ];

        // Mengganti placeholder dalam template dengan nilai dari transaksi
        return str_replace(array_keys($replacements), array_values($replacements), $template->message);
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
