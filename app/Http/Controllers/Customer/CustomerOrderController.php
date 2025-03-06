<?php

namespace App\Http\Controllers\Customer;

use App\DataTables\OrderHistoryDataTable;
use App\Enum\PaymentGatewayStatus;
use App\Exceptions\AppException;
use App\Http\Controllers\Controller;
use App\Jobs\SendWhatsAppMessageJob;
use App\Jobs\SendWhatsAppScheduledMessageJob;
use App\Models\PaymentGateway;
use App\Models\Plan;
use App\Models\Router;
use App\Support\Facades\Config;
use App\Support\Facades\Xendit;
use App\Support\Facades\Tripay;
use App\Models\Customer;
use App\Models\KeyWhatsapp;
use App\Models\Transaction;
use App\Models\WhatsappMessage;
use App\Models\WhatsAppTemplate;
use Carbon\Carbon;
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

                $messageSchedule = $this->generateBillingMessage($order, $customer);

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

                    
                    $invoice = Transaction::where('username', $customer->username)->latest('id')->first();
                    $expiredAt = $invoice->expired_at;
                    SendWhatsAppScheduledMessageJob::dispatch($customer->phonenumber, $messageSchedule, $tokenDevice, $expiredAt);
                    WhatsappMessage::create([
                        'phone'   => $customer->phonenumber,
                        'message' => $messageSchedule,
                        'date'    => $expiredAt, // Simpan sesuai jadwal pengiriman
                        'status'  => 'scheduled',
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
        $invoice = Transaction::where('username', $customer->username)->latest('id')->first();
        if (!$template) return null;

        // Data pengganti untuk template
        $replacements = [
            '#INVOICE#'              => $invoice->invoice, // Sesuaikan dengan ID invoice
            '#NOLAYANAN#'            => $order->service_number ?? '-', // Jika ada nomor layanan
            '#NAMAPELANGGAN#'        => $customer->fullname,
            '#CHANNEL#'              => strtoupper($order->payment_channel), // XENDIT, TRIPAY, dll.
            '#TGLBAYAR#'             => $order->paid_date->format('d-m-Y H:i'),
            '#SUBTOTAL#'             => number_format($order->price, 0, ',', '.'),
            '#DISKON#'               => number_format(0, 0, ',', '.'), // Sesuaikan jika ada diskon
            '#KODEUNIK#'             => number_format(0, 0, ',', '.'), // Sesuaikan jika ada kode unik
            '#PPN#'                  => number_format(0, 0, ',', '.'), // Sesuaikan jika ada PPN
            '#ADM#'                  => number_format(0, 0, ',', '.'), // Sesuaikan jika ada biaya admin
            '#TOTAL#'                => number_format($order->price, 0, ',', '.'),
            '#LAYANANAKTIFSAMPAI#'   => Carbon::parse($invoice->active_at)->translatedFormat('j F Y'),
        ];

        // Mengganti placeholder dalam template dengan nilai dari transaksi
        return str_replace(array_keys($replacements), array_values($replacements), $template->message);
    }

    private function generateBillingMessage(PaymentGateway $order, $customer)
    {
        // Ambil template pesan dari database berdasarkan tipe 'Penagihan'
        $template = WhatsAppTemplate::where('type', 'Penagihan')->first();
        $transaction = Transaction::where('username', $customer->username)->latest('id')->first();
        if (!$template) return null;

        // Data pengganti untuk template
        $replacements = [
            '#NOLAYANAN#'       => $transaction->service_number,
            '#NAMAPELANGGAN#'   => $customer->fullname,
            '#ALAMATPASANG#'    => $customer->address,
            '#INVOICE#'         => $transaction->invoice,
            '#PERIODE#'         => $transaction->periode,
            '#SUBTOTAL#'        => number_format($transaction->price, 0, ',', '.'),
            '#DISKON#'          => number_format($transaction->diskon, 0, ',', '.'),
            '#KODEUNIK#'        => $transaction->kode_unik,
            '#PPN#'             => number_format($transaction->ppn, 0, ',', '.'),
            '#ADM#'             => number_format($transaction->adm, 0, ',', '.'),
            '#TOTAL#'           => number_format($transaction->price, 0, ',', '.'),
            '#JATUHTEMPO#'      => $transaction->expired_at,
            '#VIATRANSFERBANK#' => "BCA: 1234567890 a.n PT. Contoh",
            '#VIAPAYMENTGATEWAY#' => "GoPay, ShopeePay, dll.",
        ];

        // Mengganti placeholder dengan nilai dari pelanggan
        return str_replace(array_keys($replacements), array_values($replacements), $template->message);
    }

    public function cancel(PaymentGateway $order)
    {
        $order->update([
            'status' => PaymentGatewayStatus::CANCELED,
        ]);

        return redirect()->back()->with('success', 'Transaction has been canceled');
    }

    public function history(OrderHistoryDataTable $dataTable)
    {
        return $dataTable->render('customer.order.history');
    }
}
