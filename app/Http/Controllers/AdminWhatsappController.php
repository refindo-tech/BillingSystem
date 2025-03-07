<?php

namespace App\Http\Controllers;

use App\DataTables\Tables\WhatsAppMessageDataTable;
use App\Jobs\SendWhatsAppMessageJob;
use App\Models\Customer;
use App\Models\KeyWhatsapp;
use App\Models\Router;
use App\Models\Server;
use App\Models\Transaction;
use App\Models\UserRecharge;
use App\Models\WhatsappMessage;
use App\Models\WhatsAppTemplate;
use Carbon\Carbon;
use GuzzleHttp\Promise\Create;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class AdminWhatsappController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(WhatsAppMessageDataTable $dataTable)
    {
        $server = Server::all();
        $router = Router::all();
        return $dataTable->render('admin.setting.whatsapp.index', compact('server', 'router'));
    }
    public function getData()
    {
        return DataTables::of(WhatsappMessage::query())
            ->addColumn('select', function ($row) {
                return '<input type="checkbox" name="template_ids[]" value="' . $row->id . '">';
            })
            ->rawColumns(['select'])
            ->make(true);
    }



    public function template()
    {
        $templateLayananBaru = WhatsAppTemplate::where('type', 'layanan_baru')->first();
        $templateInvoice = WhatsAppTemplate::where('type', 'invoice')->first();
        $templateIsolir = WhatsAppTemplate::where('type', 'isolir')->first();
        $templatePenagihan = WhatsAppTemplate::where('type', 'penagihan')->first();
        $templateNewTiket = WhatsAppTemplate::where('type', 'opentiket')->first();
        $templateClosedTiket = WhatsAppTemplate::where('type', 'closedtiket')->first();
        $templateUserRegis = WhatsAppTemplate::where('type', 'userregis')->first();

        return view('admin.setting.whatsapp.template', compact('templateLayananBaru', 'templateInvoice', 'templateIsolir', 'templatePenagihan', 'templateNewTiket', 'templateClosedTiket', 'templateUserRegis'));
    }

    public function storeTemplate(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'pesan_notifikasi' => 'required|string',
        ]);

        WhatsAppTemplate::updateOrCreate(
            ['type' => $request->type],  // Cari berdasarkan 'type'
            ['message' => $request->pesan_notifikasi] // Update atau buat baru
        );

        return redirect()->back()->with('success', 'Template berhasil disimpan atau diperbarui.');
    }


    public function deleteMessages(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'ids' => 'required|string' // Diterima sebagai string JSON
        ]);

        // Ubah JSON string menjadi array
        $ids = json_decode($request->ids, true);

        if (!is_array($ids) || empty($ids)) {
            return response()->json(['message' => 'Tidak ada pesan yang dipilih!'], 400);
        }

        // Hapus pesan berdasarkan ID
        WhatsappMessage::whereIn('id', $ids)->delete();

        return redirect()->back()->with('success', 'Pesan berhasil dihapus.');
    }

    public function clearHistory(Request $request)
    {
        WhatsappMessage::truncate();
        return response()->json([
            'success' => true,
            'message' => 'Riwayat pesan berhasil dihapus.'
        ]);
        
    }

    public function sendWhatsAppMessage(Request $request)
    {
        $phone = $request->phone;
        $message = $request->pesan;
        $tokenDevice = KeyWhatsapp::first()->key_device;
        SendWhatsAppMessageJob::dispatch($phone, $message, $tokenDevice);

        WhatsappMessage::create([
            'phone' => $phone,
            'message' => $message,
            'date' => now(),
            'status' => 'sent',
        ]);

        return redirect()->back()->with('success', 'Pesan berhasil dikirim.');
    }

    public function resendMessages(Request $request)
    {
        // dd( $request->all());
        $ids = $request->ids;

        // Ubah JSON string menjadi array
        $ids = json_decode($request->ids, true);

        if (!is_array($ids) || empty($ids)) {
            return redirect()->back()->with('error', 'Tidak ada pesan yang dipilih!');
        }

        $messages = WhatsappMessage::whereIn('id', $ids)->get();

        if ($messages->isEmpty()) {
            return redirect()->back()->with('error', 'Pesan tidak ditemukan.');
        }

        foreach ($messages as $message) {
            $message->update(['date' => now()]);
        }

        // API Key Fonnte
        $apiKey = KeyWhatsapp::first()->key_device;

        // Kirim pesan ke dalam queue
        foreach ($messages as $message) {
            SendWhatsAppMessageJob::dispatch($message->phone, $message->message, $apiKey);
        }

        return redirect()->back()->with('success', 'Pesan berhasil dikirim ulang.');
    }

    public function blast(Request $request)
    {
        $validation = Validator::make($request->all(), ([
            'tujuan' => 'required|string',
            'server' => 'nullable|exists:servers,id',
            'router' => 'nullable|exists:routers,id',
            'pesan' => 'required|string',
        ]));

        if ($validation->fails()) {
            return redirect()->back()->withErrors($validation)->withInput();
        }

        // dd($request->all());
        $tokenDevice = KeyWhatsapp::first()->key_device;

        // Menentukan target penerima
        $messages = [];

        switch ($request->tujuan) {
            case 'semua':
                $customers = Customer::all();
                foreach ($customers as $customer) {
                    // Kirim pesan ke antrian
                    SendWhatsAppMessageJob::dispatch($customer->phonenumber, $request->pesan, $tokenDevice);

                    // Simpan log ke database
                    $messages[] = [
                        'phone' => $customer->phonenumber,
                        'message' => $request->pesan,
                        'date' => now(),
                        'status' => 'sent',
                    ];
                }
                break;

            case 'aktif':
                $customers = UserRecharge::where('status', 'on')->with('customer')->get();
                // dd($customers);

                if ($customers->isEmpty()) {
                    return back()->with('error', 'Tidak ada pelanggan aktif.');
                }

                foreach ($customers as $item) {
                    SendWhatsAppMessageJob::dispatch($item->customer->phonenumber, $request->pesan, $tokenDevice);
                    $messages[] = [
                        'phone' => $item->customer->phonenumber,
                        'message' => $request->pesan,
                        'date' => now(),
                        'status' => 'sent',
                    ];
                }
                break;

            case 'server':
                $serverId = $request->input('server');
                $serverName = Server::find($serverId)->name;
                $customers = UserRecharge::where('server_id', $serverId)->with('customer')->get();
                // dd($customers);
                if ($customers->isEmpty()) {
                    return back()->with('error', 'Tidak ada pelanggan dengan Server' . ' ' . $serverName);
                }
                foreach ($customers as $item) {
                    SendWhatsAppMessageJob::dispatch($item->customer->phonenumber, $request->pesan, $tokenDevice);
                    $messages[] = [
                        'phone' => $item->customer->phonenumber,
                        'message' => $request->pesan,
                        'date' => now(),
                        'status' => 'sent',
                    ];
                }
                break;

            case 'router':
                $routerId = $request->input('router');
                $serverName = Server::find($routerId)->name;
                $customers = UserRecharge::where('router_id', $routerId)->with('customer')->get();
                // dd($customers);
                if ($customers->isEmpty()) {
                    return back()->with('error', 'Tidak ada pelanggan dengan Router' . ' ' . $serverName);
                }
                foreach ($customers as $item) {
                    SendWhatsAppMessageJob::dispatch($item->customer->phonenumber, $request->pesan, $tokenDevice);
                    $messages[] = [
                        'phone' => $item->customer->phonenumber,
                        'message' => $request->pesan,
                        'date' => now(),
                        'status' => 'sent',
                    ];
                }
                break;

            case 'nomor':
                if ($request->has('phone')) {
                    SendWhatsAppMessageJob::dispatch($request->phone, $request->pesan, $tokenDevice);
                    $messages[] = [
                        'phone' => $request->phone,
                        'message' => $request->pesan,
                        'date' => now(),
                        'status' => 'sent',
                    ];
                }
                break;
        }

        // Simpan ke database hanya jika ada data
        if (!empty($messages)) {
            WhatsappMessage::insert($messages);
        }

        return redirect()->back()->with('success', 'Pesan berhasil dikirim!');
    }

    public function sendBillingNotification(Request $request)
    {
        // dd($request->all());
        try {
            $customers = UserRecharge::where('status', 'on')
                ->with('customer')
                ->whereDate('expired_at', '>=', now()->toDateString())
                ->whereDate('expired_at', '<=', now()->addDays(7)->toDateString())
                ->get();

            // dd($customers, now(), now()->addDays(7));

            if ($customers->isEmpty()) {
                return back()->with('error', 'Tidak ada pelanggan aktif.');
            }

            foreach ($customers as $item) {
                $transaction = Transaction::where('username', $item->customer->username)->latest('id')->first();
                // dd($transaction);
                if ($item->isTagihan() && !$item->hasReceivedMessage('Penagihan')) {
                    $billingData = [
                        'service_number'      => $item->service_number,
                        'customer_name'       => $item->customer->fullname,
                        'invoice'             => $transaction->invoice,
                        'periode'             => Carbon::now()->locale('id')->translatedFormat('F Y'),
                        'subtotal'            => $transaction->price,
                        'diskon'              => 5000,
                        'kode_unik'           => rand(100, 999),
                        'ppn'                 => 10000,
                        'adm'                 => 2000,
                        'total'               => $transaction->price,
                        'jatuh_tempo'         => Carbon::parse($item->expired_at)->locale('id')->translatedFormat('d F Y'),
                        'via_transfer_bank'   => "Via Transfer Bank: BNI, BCA, Mandiri, BTN, BSI, Permata Bank",
                        'via_payment_gateway' => "Via Dana virtual: GoPay, ShopeePay, Dana, OVO",
                    ];

                    $message = $this->generateBillingMessage($item, $billingData);

                    if ($message) {
                        $tokenDevice = KeyWhatsapp::first()->key_device;
                        SendWhatsAppMessageJob::dispatch($item->customer->phonenumber, $message, $tokenDevice);

                        WhatsappMessage::create([
                            'phone'   => $item->customer->phonenumber,
                            'message' => $message,
                            'date'    => now(),
                            'status'  => 'sent',
                        ]);
                    }
                }
            }

            return redirect()->back()->with('success', 'Notifikasi penagihan berhasil dikirim.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Fungsi generateBillingMessage
    private function generateBillingMessage(UserRecharge $recharge, array $billingData)
    {
        $template = WhatsappTemplate::where('type', 'Penagihan')->first();
        if (!$template) return null;

        $replacements = [
            '#NOLAYANAN#'       => $billingData['service_number'],
            '#NAMAPELANGGAN#'   => $billingData['customer_name'],
            '#INVOICE#'         => $billingData['invoice'],
            '#PERIODE#'         => $billingData['periode'],
            '#SUBTOTAL#'        => number_format($billingData['subtotal'], 0, ',', '.'),
            '#DISKON#'          => number_format($billingData['diskon'], 0, ',', '.'),
            '#KODEUNIK#'        => $billingData['kode_unik'],
            '#PPN#'             => number_format($billingData['ppn'], 0, ',', '.'),
            '#ADM#'             => number_format($billingData['adm'], 0, ',', '.'),
            '#TOTAL#'           => number_format($billingData['total'], 0, ',', '.'),
            '#JATUHTEMPO#'      => $billingData['jatuh_tempo'],
            '#VIATRANSFERBANK#' => $billingData['via_transfer_bank'],
            '#VIAPAYMENTGATEWAY#' => $billingData['via_payment_gateway'],
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $template->message);
    }

    public function sendIsolateNotification(Request $request)
    {
        try {
            $customers = UserRecharge::with('customer')->where('status', 'off')
                ->whereDate('expired_at', '<', now()->toDateString())
                ->get();

            if ($customers->isEmpty()) {
                return back()->with('error', 'Tidak ada pelanggan yang diisolir.');
            }

            foreach ($customers as $item) {
                $transaction = Transaction::where('username', $item->customer->username)->latest('id')->first();
                // dd($transaction);
                if (!$item->hasReceivedMessage('Isolir')) {
                    $billingData = [
                        'service_number'      => $item->service_number,
                        'customer_name'       => $item->customer->fullname,
                        'invoice'             => $transaction->invoice,
                        'periode'             => Carbon::now()->locale('id')->translatedFormat('F Y'),
                        'total'               => $transaction->price,
                        'jatuh_tempo'         => Carbon::parse($item->expired_at)->locale('id')->translatedFormat('d F Y'),
                        'via_transfer_bank'   => "Via Transfer Bank: BNI, BCA, Mandiri, BTN, BSI, Permata Bank",
                        'via_payment_gateway' => "Via Dana virtual: GoPay, ShopeePay, Dana, OVO",
                    ];

                    $message = $this->generateIsolirMessage($item, $billingData);

                    if ($message) {
                        $tokenDevice = KeyWhatsapp::first()->key_device;
                        SendWhatsAppMessageJob::dispatch($item->customer->phonenumber, $message, $tokenDevice);

                        WhatsappMessage::create([
                            'phone'   => $item->customer->phonenumber,
                            'message' => $message,
                            'date'    => now(),
                            'status'  => 'sent',
                        ]);
                    }
                }
            }

            return redirect()->back()->with('success', 'Notifikasi isolir berhasil dikirim.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Fungsi generateIsolirMessage
    private function generateIsolirMessage(UserRecharge $recharge, array $billingData)
    {
        $template = WhatsappTemplate::where('type', 'Isolir')->first();
        if (!$template) return null;

        $replacements = [
            '#NOLAYANAN#'       => $billingData['service_number'],
            '#NAMAPELANGGAN#'   => $billingData['customer_name'],
            '#INVOICE#'         => $billingData['invoice'],
            '#PERIODE#'         => $billingData['periode'],
            '#TOTAL#'           => number_format($billingData['total'], 0, ',', '.'),
            '#JATUHTEMPO#'      => $billingData['jatuh_tempo'],
            '#VIATRANSFERBANK#' => $billingData['via_transfer_bank'],
            '#VIAPAYMENTGATEWAY#' => $billingData['via_payment_gateway'],
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $template->message);
    }

    // ini harusnya udah pindah di prepaid cuman disini dulu
    public function sendNewServiceNotification(Request $request)
    {
        try {
            $customers = UserRecharge::where('status', 'new') // Ambil pelanggan baru
                ->with('customer')
                ->get();

            if ($customers->isEmpty()) {
                return back()->with('error', 'Tidak ada pelanggan baru.');
            }

            foreach ($customers as $item) {
                if (!$item->hasReceivedMessage('Layanan Baru')) {
                    $serviceData = [
                        'service_number'  => $item->service_number,
                        'customer_name'   => $item->customer->name,
                        'alamat_pasang'   => $item->customer->installation_address,
                        'profile'         => $item->package->name,
                        'harga'           => number_format($item->package->price, 0, ',', '.'),
                        'jenis_tagihan'   => $item->billing_type,
                        'tgl_aktif'       => Carbon::parse($item->activated_at)->format('d M Y'),
                        'tgl_isolir'      => Carbon::parse($item->expired_at)->format('d M Y'),
                        'phone'           => $item->customer->phonenumber,
                        'url'             => route('client.login'),
                    ];

                    $message = $this->generateServiceMessage($item, $serviceData);

                    if ($message) {
                        $tokenDevice = KeyWhatsapp::first()->key_device;
                        SendWhatsAppMessageJob::dispatch($item->customer->phonenumber, $message, $tokenDevice);

                        WhatsappMessage::create([
                            'phone'   => $item->customer->phonenumber,
                            'message' => $message,
                            'date'    => now(),
                            'status'  => 'sent',
                        ]);
                    }
                }
            }

            return redirect()->back()->with('success', 'Notifikasi layanan baru berhasil dikirim.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    private function generateServiceMessage(UserRecharge $recharge, array $serviceData)
    {
        $template = WhatsappTemplate::where('type', 'layanan_baru')->first();
        if (!$template) return null;

        $replacements = [
            '#NOLAYANAN#'     => $serviceData['service_number'],
            '#NAMAPELANGGAN#' => $serviceData['customer_name'],
            '#ALAMATPASANG#'  => $serviceData['alamat_pasang'],
            '#PROFILE#'       => $serviceData['profile'],
            '#HARGA#'         => $serviceData['harga'],
            '#JENISTAGIHAN#'  => $serviceData['jenis_tagihan'],
            '#TGLAKTIF#'      => $serviceData['tgl_aktif'],
            '#TGLISOLIR#'     => $serviceData['tgl_isolir'],
            '#PHONE#'        => $serviceData['phone'],
            '#URL#'          => $serviceData['url'],
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $template->message);
    }
    // invoie belum gatau sistem paymentnya
    // public function sendInvoiceNotification(Request $request)
    // {
    //     try {
    //         $payments = UserRecharge::whereDate('expired_at', now()->toDateString())->get();

    //         if ($payments->isEmpty()) {
    //             return back()->with('error', 'Tidak ada pembayaran yang diterima hari ini.');
    //         }

    //         foreach ($payments as $payment) {
    //             if (!$payment->hasReceivedMessage('Invoice')) {
    //                 $invoiceData = [
    //                     'invoice'             => $payment->invoice_number,
    //                     'service_number'      => $payment->user_recharge->service_number,
    //                     'customer_name'       => $payment->user_recharge->customer->name,
    //                     'channel'             => $payment->payment_channel,
    //                     'tgl_bayar'           => Carbon::parse($payment->paid_at)->format('d M Y'),
    //                     'subtotal'            => $payment->subtotal,
    //                     'diskon'              => $payment->discount,
    //                     'kode_unik'           => $payment->unique_code,
    //                     'ppn'                 => $payment->ppn,
    //                     'adm'                 => $payment->admin_fee,
    //                     'total'               => $payment->total_paid,
    //                     'layanan_aktif_sampai' => $payment->user_recharge->expired_at
    //                         ? "Layanan aktif sampai: *" . Carbon::parse($payment->user_recharge->expired_at)->format('d M Y') . "*"
    //                         : "",
    //                 ];

    //                 $message = $this->generateInvoiceMessage($invoiceData);

    //                 if ($message) {
    //                     $tokenDevice = KeyWhatsapp::first()->key_device;
    //                     SendWhatsAppMessageJob::dispatch($payment->user_recharge->customer->phonenumber, $message, $tokenDevice);

    //                     WhatsappMessage::create([
    //                         'phone'   => $payment->user_recharge->customer->phonenumber,
    //                         'message' => $message,
    //                         'date'    => now(),
    //                         'status'  => 'sent',
    //                     ]);
    //                 }
    //             }
    //         }

    //         return redirect()->back()->with('success', 'Notifikasi invoice berhasil dikirim.');
    //     } catch (\Exception $e) {
    //         return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    //     }
    // }

    // private function generateInvoiceMessage(array $invoiceData)
    // {
    //     $template = WhatsappTemplate::where('type', 'invoice')->first();
    //     if (!$template) return null;

    //     $replacements = [
    //         '#INVOICE#'             => $invoiceData['invoice'],
    //         '#NOLAYANAN#'           => $invoiceData['service_number'],
    //         '#NAMAPELANGGAN#'       => $invoiceData['customer_name'],
    //         '#CHANNEL#'             => $invoiceData['channel'],
    //         '#TGLBAYAR#'            => $invoiceData['tgl_bayar'],
    //         '#SUBTOTAL#'            => number_format($invoiceData['subtotal'], 0, ',', '.'),
    //         '#DISKON#'              => number_format($invoiceData['diskon'], 0, ',', '.'),
    //         '#KODEUNIK#'            => $invoiceData['kode_unik'],
    //         '#PPN#'                 => number_format($invoiceData['ppn'], 0, ',', '.'),
    //         '#ADM#'                 => number_format($invoiceData['adm'], 0, ',', '.'),
    //         '#TOTAL#'               => number_format($invoiceData['total'], 0, ',', '.'),
    //         '#LAYANANAKTIFSAMPAI#'  => $invoiceData['layanan_aktif_sampai'],
    //     ];

    //     return str_replace(array_keys($replacements), array_values($replacements), $template->message);
    // }






    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
