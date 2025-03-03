<?php

namespace App\Http\Controllers;

use App\DataTables\Tables\WhatsAppMessageDataTable;
use App\Jobs\SendWhatsAppMessageJob;
use App\Models\Customer;
use App\Models\KeyWhatsapp;
use App\Models\Router;
use App\Models\Server;
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

        return view('admin.setting.whatsapp.template', compact('templateLayananBaru', 'templateInvoice', 'templateIsolir', 'templatePenagihan'));
    }

    public function storeTemplate(Request $request)
    {
        $request->validate([
            'pesan_notifikasi' => 'required|string',
        ]);

        WhatsAppTemplate::updateOrCreate(
            ['type' => $request->type],  // Cari berdasarkan 'type'
            ['message' => $request->pesan_notifikasi] // Update atau buat baru
        );

        return redirect()->route('admin:setting.whatsapp.index')
            ->with('success', 'Template berhasil disimpan atau diperbarui.');
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
        return redirect()->back()->with('success', 'Riwayat pesan berhasil dihapus.');
    }

    // public function sendBillingNotification()
    // {
    //     // Logika pengiriman notifikasi tagihan
    //     return response()->json(['message' => 'Notifikasi penagihan berhasil dikirim.']);
    // }

    // public function sendIsolateNotification()
    // {
    //     // Logika pengiriman notifikasi isolir
    //     return response()->json(['message' => 'Notifikasi isolir berhasil dikirim.']);
    // }

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

                if ($customers->isEmpty()) {
                    return back()->with('error', 'Tidak ada pelanggan aktif.');
                }

                foreach ($customers as $customer) {
                    SendWhatsAppMessageJob::dispatch($customer->phonenumber, $request->pesan, $tokenDevice);
                    $messages[] = [
                        'phone' => $customer->phonenumber,
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
                ->whereHas('customer')
                ->whereDate('expired_at', '>=', now()->toDateString())
                ->whereDate('expired_at', '<=', now()->addDays(7)->toDateString())
                ->get();

            // dd($customers, now(), now()->addDays(7));

            if ($customers->isEmpty()) {
                return back()->with('error', 'Tidak ada pelanggan aktif.');
            }

            foreach ($customers as $item) {
                if ($item->isTagihan() && !$item->hasReceivedMessage('Penagihan')) {
                    $billingData = [
                        'service_number'      => $item->service_number,
                        'customer_name'       => $item->customer->name,
                        'invoice'             => 'INV-123456',
                        'periode'             => Carbon::now()->format('F Y'),
                        'subtotal'            => 100000,
                        'diskon'              => 5000,
                        'kode_unik'           => rand(100, 999),
                        'ppn'                 => 10000,
                        'adm'                 => 2000,
                        'total'               => 107000,
                        'jatuh_tempo'         => Carbon::now()->addDays(5),
                        'via_transfer_bank'   => "BCA: 1234567890 a.n PT. Contoh",
                        'via_payment_gateway' => "GoPay, ShopeePay, dll.",
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
            '#JATUHTEMPO#'      => Carbon::parse($billingData['jatuh_tempo'])->format('d M Y'),
            '#VIATRANSFERBANK#' => $billingData['via_transfer_bank'],
            '#VIAPAYMENTGATEWAY#' => $billingData['via_payment_gateway'],
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $template->message);
    }

    public function sendIsolateNotification(Request $request)
    {
        try {
            $customers = UserRecharge::whereHas('customer')
                ->whereDate('expired_at', '<', now()->toDateString())
                ->get();

            // dd($customers,now()->toDateString());

            if ($customers->isEmpty()) {
                return back()->with('error', 'Tidak ada pelanggan yang diisolir.');
            }

            foreach ($customers as $item) {
                if (!$item->hasReceivedMessage('Isolir')) {
                    $billingData = [
                        'service_number'      => $item->service_number,
                        'customer_name'       => $item->customer->name,
                        'invoice'             => 'INV-123456',
                        'periode'             => Carbon::now()->format('F Y'),
                        'total'               => 107000,
                        'jatuh_tempo'         => Carbon::now()->addDays(3),
                        'via_transfer_bank'   => "BCA: 1234567890 a.n PT. Contoh",
                        'via_payment_gateway' => "GoPay, ShopeePay, dll.",
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
            '#JATUHTEMPO#'      => Carbon::parse($billingData['jatuh_tempo'])->format('d M Y'),
            '#VIATRANSFERBANK#' => $billingData['via_transfer_bank'],
            '#VIAPAYMENTGATEWAY#' => $billingData['via_payment_gateway'],
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $template->message);
    }



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
