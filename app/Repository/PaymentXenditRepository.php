<?php

namespace App\Repository;

use App\Enum\PaymentGatewayStatus;
use App\Enum\RechargeGateway;
use App\Exceptions\AppException;
use App\Models\Customer;
use App\Models\PaymentGateway;
use App\Support\Facades\Config;
use App\Support\Package;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PaymentXenditRepository
{
    protected string $baseUrl;

    protected Collection $config;

    public function __construct()
    {
        $this->baseUrl = config('payment.xendit.base_url');
        $this->config = Config::all()->only([
            'xendit_secret_key',
            'xendit_channels',
            'xendit_verification_token',
        ]);
    }

    public function updateConfig(array $data)
    {
        foreach ($data as $key => $value) {
            if (! (strpos($key, 'xendit_') !== false)) {
                throw new Exception('invalid config key');
            }
            if (is_array($value)) {
                $value = implode(',', $value);
            }
            Config::set($key, $value);
        }
    }

    public function validateConfig()
    {
        if (empty($this->config->get('xendit_secret_key'))) {
            throw new AppException('Admin has not yet setup Xendit payment gateway, please tell admin');
        }
    }

    public function createTransaction(PaymentGateway $trx, Customer $user)
    {

        $json = [
            'external_id' => (string) $trx['id'],
            'amount' => $trx['price'],
            'description' => $trx['plan_name'],
            'channel_code' => $trx['payment_channel'],
            'customer' => [
                'mobile_number' => $user['phonenumber'],
            ],
            'customer_notification_preference' => [
                'invoice_created' => ['whatsapp', 'sms'],
                'invoice_reminder' => ['whatsapp', 'sms'],
                'invoice_paid' => ['whatsapp', 'sms'],
                'invoice_expired' => ['whatsapp', 'sms'],
            ],
            'payment_methods ' => explode(',', $this->config->get('xendit_channels')),
            'success_redirect_url' => route('customer:order.check', $trx),
            'failure_redirect_url' => route('customer:order.check', $trx),
        ];
        
        $result = Http::withBasicAuth($this->config->get('xendit_secret_key'), '')
            ->post($this->baseUrl.'/invoices', $json)->collect();
        if (! $result->get('id')) {
            throw new AppException('Failed to create transaction: '.$result->get('error_code'));
        }
        $trx->gateway_trx_id = $result['id'];
        $trx->pg_url_payment = $result['invoice_url'];
        $trx->pg_request = json_encode($result);
        $trx->expired_date = date('Y-m-d H:i:s', strtotime($result['expiry_date']));
        $trx->save();

        return redirect($result['invoice_url']);
    }

    public function getStatus(PaymentGateway $trx, Customer $user)
{
    if ($trx->status == PaymentGatewayStatus::PAID) {
        return true;
    }

    $result = Http::withBasicAuth($this->config->get('xendit_secret_key'), '')
        ->get($this->baseUrl.'/invoices/'.$trx['gateway_trx_id'])->collect();

    if ($result['status'] == 'PENDING') {
        throw new AppException('Transaction still unpaid.');
    }

    DB::beginTransaction();
    try {
        // Lock transaction to prevent duplicate processing
        $trx = PaymentGateway::where('id', $trx->id)->lockForUpdate()->first();

        // Double-check if already processed
        if ($trx->status == PaymentGatewayStatus::PAID) {
            DB::rollBack();
            return true;
        }

        if (in_array($result['status'], ['PAID', 'SETTLED'])) {
            Package::activatePackage($trx->userRecharge, RechargeGateway::XENDIT, $result['payment_channel'], $trx->transaction_type);

            $trx->pg_paid_response = json_encode($result);
            $trx->payment_method = $result['payment_method'];
            $trx->payment_channel = $result['payment_channel'];
            $trx->paid_date = date('Y-m-d H:i:s', strtotime($result['updated']));
            $trx->status = PaymentGatewayStatus::PAID;
        } elseif ($result['status'] == 'EXPIRED') {
            $trx->pg_paid_response = json_encode($result);
            $trx->status = PaymentGatewayStatus::FAILED;
            throw new AppException('Transaction expired.');
        }

        $trx->save();
        DB::commit();
        return true;

    } catch (Exception $e) {
        DB::rollBack();
        Log::error('Xendit getStatus error: ' . $e->getMessage());
        throw new AppException('Failed to process transaction.');
    }
}

}
