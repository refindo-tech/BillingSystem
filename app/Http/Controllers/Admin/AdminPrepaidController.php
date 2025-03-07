<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\UserRechargeDataTable;
use App\DataTables\VoucherDataTable;
use App\Enum\PlanType;
use App\Enum\RechargeGateway;
use App\Enum\VoucherFormat;
use App\Enum\VoucherStatus;
use App\Enum\ValidityCycle;
use App\Enum\ValidityUnit;
use App\Enum\UpgradeType;
use App\Enum\PaymentGatewayStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Prepaid\PrepaidUserRequest;
use App\Http\Requests\Admin\Prepaid\PrepaidUserUpdateRequest;
use App\Http\Requests\Admin\Prepaid\PrepaidVoucherRequest;
use App\Jobs\SendWhatsAppMessageJob;
use App\Jobs\SendWhatsAppScheduledMessageJob;
use App\Models\Customer;
use App\Models\KeyWhatsapp;
use App\Models\Plan;
use App\Models\Router;
use App\Models\Transaction;
use App\Models\UserRecharge;
use App\Models\Voucher;
use App\Models\Server;
use App\Models\PaymentGateway;
use App\Models\WhatsappMessage;
use App\Models\WhatsAppTemplate;
use App\Support\Facades\Config;
use App\Support\Facades\Log;
use App\Support\Facades\Xendit;
use App\Support\Facades\Tripay;
use App\Support\Lang;
use App\Support\Mikrotik;
use App\Support\Package;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;


class AdminPrepaidController extends Controller
{
    public function user(UserRechargeDataTable $dataTable)
    {
        return $dataTable->render('admin.prepaid.user.list');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function createUser()
    {

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

        // dd($activeChannels);

        $mode = 'add';
        $prefix = '00000';
        $customers = Customer::all()->mapWithKeys(fn($customer) => [

            $customer->id => (strlen($prefix) > strlen($customer->id) ? substr($prefix, 0, strlen($prefix) - strlen($customer->id)) : '') . $customer->id . ' - ' . $customer->fullname . ' - ' . $customer->email,

        ]);
        $planTypes = array_column(PlanType::cases(), 'value', 'value');
        $validityCycles = array_column(ValidityCycle::cases(), 'value', 'value');
        $defaultPlanType = PlanType::HOTSPOT;
        $defaultValidityCycle = ValidityCycle::PROFILE;
        return view('admin.prepaid.user.form', compact('mode', 'customers', 'planTypes', 'defaultPlanType', 'validityCycles', 'defaultValidityCycle', 'activeChannels'));
    }

    public function rechargeUser(Customer $user)
    {

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

        $mode = 'add';
        $prefix = '00000';
        $customers = Customer::all()->mapWithKeys(fn($customer) => [

            $customer->id => (strlen($prefix) > strlen($customer->id) ? substr($prefix, 0, strlen($prefix) - strlen($customer->id)) : '') . $customer->id . ' - ' . $customer->fullname . ' - ' . $customer->email,

        ]);
        $user['customer_id'] = $user->id;
        $planTypes = array_column(PlanType::cases(), 'value', 'value');
        $defaultPlanType = PlanType::HOTSPOT;
        $validityCycles = array_column(ValidityCycle::cases(), 'value', 'value');
        $defaultValidityCycle = ValidityCycle::PROFILE;

        return view('admin.prepaid.user.form', compact('mode', 'customers', 'planTypes', 'defaultPlanType', 'user', 'validityCycles', 'defaultValidityCycle', 'activeChannels'));
    }

    public function editUser(UserRecharge $user)
    {
        $mode = 'edit';
        $customer = Customer::findOrFail($user->customer_id);
        $customers = [
            $customer->id => $customer->username . ' - ' . $customer->fullname . ' - ' . $customer->email,
        ];

        $planTypes = array_column(PlanType::cases(), 'value', 'value');
        $planOptions = Plan::where('type', $user->plan->type)
            ->where('router_id', $user->router_id)
            ->get()
            ->mapWithKeys(fn($plan) => [
                $plan->id => $plan->name . ' - ' . Lang::moneyFormat($plan->price),
            ]);

        $routerOptions = Router::all()->mapWithKeys(fn($router) => [
            $router->id => $router->name . ' - ' . $router->ip_address,
        ]);

        $serverOptions = Server::where('router_id', $user->router_id)->get()->mapWithKeys(fn($server) => [
            $server->id => $server->name,
        ]);

        $validityCycles = array_column(ValidityCycle::cases(), 'value', 'value');
        $upgradeTypes = array_map(fn($value) => $value . ' - ' . UpgradeType::from($value)->description(), array_column(UpgradeType::cases(), 'value', 'value'));

        return view('admin.prepaid.user.form-update', compact(
            'mode',
            'customers',
            'planTypes',
            'planOptions',
            'routerOptions',
            'serverOptions',
            'validityCycles',
            'upgradeTypes'
        ))->with([
            'defaultPlanType' => $user->plan->type,
            'defaultRouterId' => $user->plan->router_id,
            'serviceNumber' => $user->service_number,
            'defaultValidityCycle' => $user->validity_cycle,
            'defaultUpgradeType' => UpgradeType::RECHARGE,
            'user' => $user,
        ]);
    }

    public function storeUser(PrepaidUserRequest $request)
    {
        try {
            $customer = Customer::findOrFail($request->customer_id);
            $router = Router::findOrFail($request->router_id);
            $plan = Plan::findOrFail($request->plan_id);
            $username = $request->username;
            $password = $request->pppoe_password;
            $server_id = $request->server_id;
            $payment_channel = $request->payment_channel;

            

            // Recharge user
            $userRecharge = Package::rechargeUser(
                $customer,
                $router,
                $plan,
                RechargeGateway::RECHARGE,
                $payment_channel,
                $request->service_number,
                $request->validity_cycle,
                $request->expired_at,
                $username,
                $password,
                $server_id
            );


            
            
            // Generate pesan WhatsApp
            $message = $this->generateRechargeMessage($customer, $plan, $request);
            $messageSchedule = $this->generateBillingMessage($customer, $plan, $request);

            // Kirim pesan WhatsApp jika nomor HP tersedia
            if (!empty($customer->phonenumber) && $message) {
                $tokenDevice = KeyWhatsapp::first()->key_device;
                SendWhatsAppMessageJob::dispatch($customer->phonenumber, $message, $tokenDevice);

                // Simpan log pesan ke database
                WhatsappMessage::create([
                    'phone'   => $customer->phonenumber,
                    'message' => $message,
                    'date'    => now(),
                    'status'  => 'sent',
                ]);

                if ($plan->validity_unit === ValidityUnit::MONTHS) {
                    $expiredAt = Carbon::parse($request->expired_at)->subDays(7)->timestamp;
                    SendWhatsAppScheduledMessageJob::dispatch($customer->phonenumber, $messageSchedule, $tokenDevice, $expiredAt);
                    WhatsappMessage::create([
                        'phone'   => $customer->phonenumber,
                        'message' => $messageSchedule,
                        'date'    => $expiredAt, // Simpan sesuai jadwal pengiriman
                        'status'  => 'scheduled',
                    ]);
                } elseif ($plan->validity_unit === ValidityUnit::DAYS) {
                    $expiredAt = Carbon::parse($request->expired_at)->subDays(1)->timestamp;
                    SendWhatsAppScheduledMessageJob::dispatch($customer->phonenumber, $messageSchedule, $tokenDevice, $expiredAt);
                    WhatsappMessage::create([
                        'phone'   => $customer->phonenumber,
                        'message' => $messageSchedule,
                        'date'    => $expiredAt, // Simpan sesuai jadwal pengiriman
                        'status'  => 'scheduled',
                    ]);
                }
            }

            Log::put('Recharge account '.$customer->username, auth()->user());

            return redirect()->route('admin:prepaid.user.index')->with('success', __('success.created'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    private function generateRechargeMessage(Customer $customer, Plan $plan, $request)
    {
        // Ambil template pesan dari database berdasarkan tipe 'Registrasi' atau 'Isi Ulang'
        $template = WhatsAppTemplate::where('type', 'layanan_baru')->first();

        if (!$template) return null;

        // Data pengganti untuk template
        $replacements = [
            '#NOLAYANAN#'     => $request->service_number, // Format nomor layanan
            '#NAMAPELANGGAN#' => $customer->fullname,
            '#ALAMATPASANG#'  => $customer->address,
            '#PROFILE#'       => $plan->name,
            '#HARGA#'         => number_format($plan->price, 0, ',', '.'),
            '#JENISTAGIHAN#'  => $request->validity_cycle,
            '#TGLAKTIF#'      => date('d-m-Y', strtotime($request->active_at)),
            '#TGLISOLIR#'     => date('d-m-Y', strtotime($request->expired_at)),
            '#PHONE#'         => $customer->phonenumber,
            '#URL#'           => route('customer:dashboard'), // Contoh link ke profil pelanggan
        ];

        // Mengganti placeholder dengan nilai dari pelanggan
        return str_replace(array_keys($replacements), array_values($replacements), $template->message);
    }

    private function generateBillingMessage(Customer $customer, Plan $plan, $request)
    {
        // Ambil template pesan dari database berdasarkan tipe 'Penagihan'
        $template = WhatsAppTemplate::where('type', 'Penagihan')->first();
        if (!$template) return null;

        // Data pengganti untuk template
        $replacements = [
            '#NOLAYANAN#'       => $request->service_number,
            '#NAMAPELANGGAN#'   => $customer->fullname,
            '#ALAMATPASANG#'    => $customer->address,
            // '#INVOICE#'         => $transaction->invoice,
            '#PERIODE#'         => $request->periode,
            '#SUBTOTAL#'        => number_format($plan->price, 0, ',', '.'),
            '#DISKON#'          => number_format($request->diskon, 0, ',', '.'),
            '#KODEUNIK#'        => $request->kode_unik,
            '#PPN#'             => number_format($request->ppn, 0, ',', '.'),
            '#ADM#'             => number_format($request->adm, 0, ',', '.'),
            '#TOTAL#'           => number_format($plan->price, 0, ',', '.'),
            '#JATUHTEMPO#'      => $request->expired_at,
            '#VIATRANSFERBANK#' => "Via Transfer Bank: BNI, BCA, Mandiri, BTN, BSI, Permata Bank",
            '#VIAPAYMENTGATEWAY#' => "Via Dana virtual: GoPay, ShopeePay, Dana, OVO",
        ];

        // Mengganti placeholder dengan nilai dari pelanggan
        return str_replace(array_keys($replacements), array_values($replacements), $template->message);
    }

    public function updateUser(PrepaidUserUpdateRequest $request, UserRecharge $user)
    {

        // dd($activeGateway);
        $customer = Customer::findOrFail($request->customer_id);
        $plan = Plan::findOrFail($request->plan_id);
        $newPlan = Plan::findOrFail($request->new_plan_id);
        $username = $request->username;
        $password = $request->pppoe_password;

        Package::changeTo($customer, $newPlan, $user, $username, $password);

        $user->plan_id = $newPlan->id;
        $user->expired_at = match ($request->upgrade_type) {
            UpgradeType::RECHARGE->value => date('Y-m-d H:i:s', strtotime($user->expired_at . ' +1 month')),
            // UpgradeType::DEACTIVATE->value => date('Y-m-d H:i:s', strtotime($user->expired_at . ' -1 day')),
            default => $user->expired_at,
        };
        $user->save();
        Log::put('Update account ' . $customer->username, auth()->user());

        return redirect()->route('admin:prepaid.user.index')->with('success', __('success.updated'));
    }

    public function destroyUser(UserRecharge $user)
    {
        if ($user->plan->is_radius) {
            //TODO: Radius::customerDeactivate
        } else {
            $mikrotik = $user->router;
            $client = Mikrotik::getClient($mikrotik->ip_address, $mikrotik->username, $mikrotik->password);
            if ($user->type == PlanType::HOTSPOT) {
                Mikrotik::removeHotspotUser($client, $user->username);
                Mikrotik::removeHotspotActiveUser($client, $user->username);
            } else {
                Mikrotik::removePpoeUser($client, $user->username);
                Mikrotik::removePpoeActive($client, $user->username);
            }
        }
        PaymentGateway::where('user_recharge_id', $user->id)->delete();
        $user->delete();
        Log::put('Delete account ' . $user->username, auth()->user());

        return redirect()->route('admin:prepaid.user.index')->with('success', __('success.deleted'));
    }

    public function showInvoice(Transaction $invoice)
    {
        $admin = auth()->user();
        $config = Config::all();

        return view('admin.prepaid.invoice.show', compact('invoice', 'admin', 'config'));
    }

    public function printInvoice(Transaction $invoice)
    {
        $admin = auth()->user();
        $config = Config::all();

        return view('admin.prepaid.invoice.print', compact('invoice', 'admin', 'config'));
    }

    public function voucher(VoucherDataTable $dataTable)
    {
        return $dataTable->render('admin.prepaid.voucher.list');
    }

    public function createVoucher()
    {
        $mode = 'add';
        $planTypes = array_column(PlanType::cases(), 'value', 'value');
        $defaultPlanType = PlanType::HOTSPOT;
        $voucherFormats = array_column(VoucherFormat::cases(), 'name', 'value');
        $defaultVoucherFormat = Config::get('voucher_format') ?: VoucherFormat::UPPERCASE->value;
        $defaultPrefix = Config::get('voucher_prefix');

        return view('admin.prepaid.voucher.form', compact('mode', 'planTypes', 'defaultPlanType', 'voucherFormats', 'defaultVoucherFormat', 'defaultPrefix'));
    }

    public function storeVoucher(PrepaidVoucherRequest $request)
    {
        if (!empty($request->prefix)) {
            Config::set('voucher_prefix', $request->prefix);
        }
        Config::set('voucher_format', $request->format);

        for ($i = 0; $i < $request->count; $i++) {
            $code = strtoupper(substr(md5(time() . rand(10000, 99999)), 0, $request->length));
            $voucherFormat = VoucherFormat::from($request->format);

            if ($voucherFormat == VoucherFormat::lowercase) {
                $code = strtolower($code);
            } elseif ($voucherFormat == VoucherFormat::RaNdoM) {
                $code = Lang::randomUpLowCase($code);
            }

            // Map 'plan_type' to 'type'
            $data = $request->except('plan_type');
            $data['type'] = $request->plan_type;
            $data['code'] = $request->prefix . $code;

            Voucher::create($data);
        }

        Log::put($request->count . ' vouchers created', auth()->user());

        return redirect()->route('admin:prepaid.voucher.index')->with('success', __('success.created'));
    }


    public function destroyVoucher(Voucher $voucher)
    {
        $voucher->delete();
        Log::put('Delete Voucher ' . $voucher->code, auth()->user());

        return redirect()->route('admin:prepaid.voucher.index')->with('success', __('success.deleted'));
    }

    public function refillAccount()
    {
        $customers = Customer::all()->mapWithKeys(fn($customer) => [
            $customer->id => $customer->username . ' - ' . $customer->fullname . ' - ' . $customer->email,
        ]);

        return view('admin.prepaid.refill-account', compact('customers'));
    }

    public function storeRefillAccount(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => ['required', Rule::exists(Customer::class, 'id')],
            'voucher_code' => 'required',
        ]);

        $customer = Customer::findOrFail($validated['customer_id']);
        /** @var Voucher $voucher */
        $voucher = Voucher::where('code', $validated['voucher_code'])->where('status', VoucherStatus::UNUSED)->first();
        if (! $voucher) {
            return redirect()->back()->with('error', 'Invalid voucher code');
        }
        Package::rechargeUser($customer, $voucher->router, $voucher->plan, RechargeGateway::VOUCHER, $voucher->code);
        $voucher->status = VoucherStatus::USED;
        $voucher->customer_id = $customer->id;
        $voucher->save();
        $invoice = Transaction::where('username', $customer->username)
            ->latest('id')->first();

        Log::put('Refill Account ' . $customer->username, auth()->user());

        return redirect()->route('admin:prepaid.invoice.show', $invoice);
    }

    public function serviceNumber(Request $request)
    {

        if ($request->has('customer_id')) {
            $prefix = '00000';
            $serviceNumber = date('y');
            $serviceNumber .= (strlen($prefix) > strlen($request->customer_id) ? substr($prefix, 0, strlen($prefix) - strlen($request->customer_id)) : '') . $request->customer_id;
            $prefix2 = '00';
            // $serviceCount= UserRecharge::where('customer_id', $request->customer_id)->count() + 1;
            $userRecharge = UserRecharge::where('customer_id', $request->customer_id)->latest('id')->first();
            $serviceCount = $userRecharge ? (int)substr($userRecharge->service_number, -2) + 1 : 1;
            $serviceNumber .= (strlen($prefix2) > strlen($serviceCount) ? substr($prefix2, 0, strlen($prefix2) - strlen($serviceCount)) : '') . $serviceCount;
        } else {
            $serviceNumber = '';
        }

        return response()->json($serviceNumber);
    }

    public function expiredAt(Request $request)
    {
        if ($request->has('validity_cycle')) {
            $validityCycle = ValidityCycle::from($request->validity_cycle);
            $activeAt = $request->active_at;
            $expiredAt = null;
            if ($validityCycle == ValidityCycle::PROFILE) {
                $plan = Plan::findOrFail($request->plan_id);
                $expiredAt = match ($plan->validity_unit) {
                    ValidityUnit::DAYS => date('Y-m-d H:i:s', strtotime($activeAt . ' +' . $plan->validity . ' days')),
                    ValidityUnit::MONTHS => date('Y-m-d H:i:s', strtotime($activeAt . ' +' . $plan->validity . ' months')),
                    ValidityUnit::HRS => date('T-m-d H:i:s', strtotime($activeAt . ' +' . $plan->validity . ' hours')),
                    ValidityUnit::MINS => date('Y-m-d H:i:s', strtotime($activeAt . ' +' . $plan->validity . ' minutes')),
                };
            } elseif ($validityCycle == ValidityCycle::MONTHLY) {
                $expiredAt = date('Y-m-d H:i:s', strtotime($activeAt . ' +1 month'));
                $expiredAt = date('Y-m-04 H:i:s', strtotime($expiredAt));
            } elseif ($validityCycle == ValidityCycle::FIXED) {
                $expiredAt = date('Y-m-d H:i:s', strtotime($activeAt . ' +1 month'));
            }
        } else {
            $expiredAt = 'aaa';
        }

        return response()->json($expiredAt);
    }

    public function upgradeOption(Request $request)
    {
        if ($request->has(['id', 'upgrade_type'])) {
            $user = UserRecharge::findOrFail($request->id);
            $plan = Plan::findOrFail($user->plan_id);

            if ($request->upgrade_type == UpgradeType::RECHARGE->value) {
                //pluck the id and name of the user's current plan
                $plans = collect([$plan->id => $plan->name . ' - ' . Lang::moneyFormat($plan->price)]);
                return response()->json($plans);
            }

            $plans = Plan::where('type', $plan->type)->get()->filter(function ($value) use ($plan, $request) {
                return match ($request->upgrade_type) {
                    UpgradeType::UPGRADE->value => $value->price > $plan->price,
                    UpgradeType::DOWNGRADE->value => $value->price < $plan->price,
                    default => true,
                };
            })->map(function ($value) use ($user) {
                $value['price'] = $this->calculatePrice($user->recharged_at, $user->expired_at, $user->plan->price, $value->price);
                return $value;
            });

            //pluck the id and name
            $plans = $plans->mapWithKeys(fn($plan) => [$plan->id => $plan->name . ' - ' . Lang::moneyFormat($plan->price)]);

            return response()->json($plans);
        }

        return response()->json([]);
    }

    private function calculatePrice($startDateTime, $endDateTime, $oldPlanPrice, $newPlanPrice)
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
        //round to the nearest 500, ex: 932468.4606060 -> 932500
        return round($totalPrice / 500) * 500;
    }
}
