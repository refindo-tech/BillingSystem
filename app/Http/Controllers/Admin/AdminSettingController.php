<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\AdminUserDataTable;
use App\Enum\UserType;
use App\Enum\VoucherFormat;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Setting\SettingUserRequest;
use App\Http\Requests\Admin\Setting\SettingXenditRequest;
use App\Http\Requests\Admin\Setting\SettingTripayRequest;
use App\Models\KeyWhatsapp;
use App\Models\User;
use App\Support\Facades\Config;
use App\Support\Facades\Xendit;
use App\Support\Facades\Tripay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class AdminSettingController extends Controller
{
    public function xendit()
    {
        $channels = config('payment.xendit.channels');
        $xendit = [
            'xendit_secret_key' => Config::get('xendit_secret_key'),
            'xendit_verification_token' => Config::get('xendit_verification_token'),
            'xendit_channels' => Config::get('xendit_channels') ? explode(',', Config::get('xendit_channels')) : [],
        ];
        $activeGateway = Config::get('active_payment_gateway');

        return view('admin.setting.xendit', compact('channels', 'xendit', 'activeGateway'));
    }

    public function updateXendit(SettingXenditRequest $request)
    {
        Xendit::updateConfig($request->validated());

        return redirect()->back()->with('success', 'Xendit setting has been updated');
    }

    public function tripay()
    {
        $channels = config('payment.tripay.channels');
        $tripay = [
            'tripay_api_key'      => Config::get('tripay_api_key'),
            'tripay_private_key'  => Config::get('tripay_private_key'),
            'tripay_merchant_code'=> Config::get('tripay_merchant_code'),
            'tripay_environment' => Config::get('tripay_environment'),
            'tripay_channels'     => Config::get('tripay_channels') ? explode(',', Config::get('tripay_channels')) : [],
        ];
        $activeGateway = Config::get('active_payment_gateway');

        return view('admin.setting.tripay', compact('channels', 'tripay', 'activeGateway'));
    }

    public function updateTripay(SettingTripayRequest $request)
    {

       
        Tripay::updateConfig($request->validated());

        return redirect()->back()->with('success', 'Tripay setting has been updated');
    }

    public function paymentGateway(): \Illuminate\View\View
    {
        $tripayChannels = config('payment.tripay.channels');
        $xenditChannels = config('payment.xendit.channels');
        $activeGateway = Config::get('active_payment_gateway');
        if ($activeGateway === '') {
            $activeGateway = 'tripay';
        }

        $tripay = [
            'tripay_api_key'      => Config::get('tripay_api_key'),
            'tripay_private_key'  => Config::get('tripay_private_key'),
            'tripay_merchant_code'=> Config::get('tripay_merchant_code'),
            'tripay_environment' => Config::get('tripay_environment'),
            'tripay_channels'     => Config::get('tripay_channels') ? explode(',', Config::get('tripay_channels')) : [],
        ];

        $xendit = [
            'xendit_secret_key' => Config::get('xendit_secret_key'),
            'xendit_verification_token' => Config::get('xendit_verification_token'),
            'xendit_channels' => Config::get('xendit_channels') ? explode(',', Config::get('xendit_channels')) : [],
        ];


        return view('admin.setting.payment-gateway', compact('tripayChannels', 'xenditChannels', 'tripay', 'xendit', 'activeGateway'));
    }

    public function setActiveGateway(Request $request)
    {
        $request->validate([
            'gateway' => 'required|in:xendit,tripay',
        ]);

        Config::set('active_payment_gateway', $request->gateway);

        return redirect()->back()->with('success', ucfirst($request->gateway) . ' has been set as the active payment gateway');
    }

    public function whatsappGateway()
    {
        
        // whatsapp
        $keyWhatsapp = KeyWhatsapp::first();
        return view('admin.setting.whatsapp-gateaway', compact('keyWhatsapp'));
    }

    public function whatsappGatewayStore(Request $request)
    {
        $validator = Validator::make($request->all(), ([
            'fonnte_key_device' => 'required|string',
            'fonnte_key_account' => 'string',
            'phone' => 'required|numeric',
        ]));

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        // Jika API Key valid, lanjut simpan ke database
        $keyWhatsapp = KeyWhatsapp::first(); // Ambil data pertama jika ada

        if ($keyWhatsapp) {
            // Jika sudah ada, update
            $keyWhatsapp->update([
                'key_device' => $request->fonnte_key_device,
                'key_account' => $request->fonnte_key_account,
                'phone' => $request->phone,
            ]);
        } else {
            // Jika belum ada, tambahkan baru
            KeyWhatsapp::create([
                'key_device' => $request->fonnte_key_device,
                'key_account' => $request->fonnte_key_account,
                'phone' => $request->phone,
            ]);
        }

        return redirect()->back()->with('success', 'WhatsApp Gateway successfully updated and connected to Fonnte!');
    }

    public function checkWhatsappStatus()
    {
        $keyWhatsapp = KeyWhatsapp::first();

        if (!$keyWhatsapp) {
            return back()->with('error', 'Belum ada API Key yang disimpan.');
        }

        $response = $keyWhatsapp->checkDeviceStatus();

        if ($response['status'] === true) {
            $keyWhatsapp->update(['status' => $response['device_status']]);
            return back()->with('success', 'Status perangkat diperbarui: ' . $response['device_status']);
        } else {
            return back()->with('error', 'Gagal mendapatkan status perangkat.');
        }
    }

    public function general()
    {
        $config = Config::all();
        $voucherFormats = array_column(VoucherFormat::cases(), 'name', 'value');

        return view('admin.setting.general', compact('config', 'voucherFormats'));
    }

    public function updateGeneral(Request $request)
    {
        $request->validate([
            'CompanyName' => 'required|string',
        ]);

        foreach ($request->all() as $key => $config) {
            Config::set($key, $config ?? '');
        }

        return redirect()->back()->with('success', 'General setting has been updated');
    }

    public function localisation()
    {
        $config = Config::all();

        return view('admin.setting.localisation', compact('config'));
    }

    public function updateLocalisation(Request $request)
    {
        foreach ($request->all() as $key => $config) {
            Config::set($key, $config ?? '');
        }

        return redirect()->back()->with('success', 'Localisation setting has been updated');
    }

    public function listUser(AdminUserDataTable $dataTable)
    {
        return $dataTable->render('admin.setting.user.list');
    }

    public function createUser()
    {
        $mode = 'add';
        $userTypes = collect(UserType::cases())->flatMap(fn($type) => [$type->value => $type->label()]);

        return view('admin.setting.user.form', compact('mode', 'userTypes'));
    }

    public function storeUser(SettingUserRequest $request)
    {
        User::create($request->validated());

        return redirect()->route('admin:setting.user.index')->with('success', 'User has been created');
    }

    public function destroyUser(User $user)
    {
        $user->delete();

        return redirect()->back()->with('success', 'User has been deleted');
    }

    public function editUser(User $user)
    {
        $mode = 'edit';
        $userTypes = collect(UserType::cases())->flatMap(fn($type) => [$type->value => $type->label()]);

        return view('admin.setting.user.form', compact('mode', 'userTypes', 'user'));
    }

    public function updateUser(SettingUserRequest $request, User $user)
    {
        $user->update(array_filter($request->validated()));

        return redirect()->route('admin:setting.user.index')->with('success', 'User has been updated');
    }
}
