<?php

namespace App\Http\Controllers\Customer;

use App\DataTables\OrderHistoryDataTable;
use App\Enum\PaymentGatewayStatus;
use App\Enum\PendingUserRechargeStatus;
use App\Exceptions\AppException;
use App\Http\Controllers\Controller;
use App\Jobs\SendWhatsAppMessageJob;
use App\Models\PaymentGateway;
use App\Models\PendingUserRecharge;
use App\Models\Plan;
use App\Models\Router;
use App\Support\Facades\Config;
use App\Support\Facades\Xendit;
use App\Support\Facades\Tripay;
use App\Models\Customer;
use App\Models\KeyWhatsapp;
use App\Models\WhatsappMessage;
use App\Models\WhatsAppTemplate;
use Illuminate\Support\Collection;

class CustomerOrderController extends Controller
{
    public function index()
    {
        $routers = Router::whereEnabled(true)->get();
        return view('customer.order.list', compact('routers'));
    }

    public function buy(Plan $plan)
    {
        $user = auth()->user();

        if (strpos($user->email, '@') === false) {
            return redirect()->route('customer:profile.edit')->with('error', 'Please enter your email address');
        }

        // Get active payment gateway
        $activeGateway = Config::get('active_payment_gateway');
        //if empty, set default to xendit
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

        // Check for existing unpaid transaction
        $order = PaymentGateway::where('username', $user->username)
            ->where('status', PaymentGatewayStatus::UNPAID)
            ->first();

        if ($order && $order->pg_url_payment) {
            return redirect()->route('customer:order.detail', $order)->with('error', 'You already have an unpaid transaction. Please cancel or pay it.');
        }

        if (empty($order)) {
            $order = PaymentGateway::create([
                'username' => $user->username,
                'gateway' => $activeGateway,
                'plan_id' => $plan->id,
                'plan_name' => $plan->name,
                'router_id' => $plan->router->id,
                'router_name' => $plan->router->name,
                'price' => $plan->price,
                'status' => PaymentGatewayStatus::UNPAID,
            ]);
        } else {
            $order->update([
                'username' => $user->username,
                'gateway' => $activeGateway,
                'plan_id' => $plan->id,
                'plan_name' => $plan->name,
                'router_id' => $plan->router->id,
                'router_name' => $plan->router->name,
                'price' => $plan->price,
                'status' => PaymentGatewayStatus::UNPAID,
            ]);
        }

        // Process transaction based on active gateway
        return $activeGateway === 'xendit'
            ? Xendit::createTransaction($order, $user)
            : Tripay::createTransaction($order, $user);
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
