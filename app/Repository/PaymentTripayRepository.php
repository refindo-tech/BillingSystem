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

class PaymentTripayRepository
{
    protected string $baseUrl;
    protected Collection $config;

    public function __construct()
    {
        
        $this->config = Config::all()->only([
            'tripay_api_key',
            'tripay_private_key',
            'tripay_merchant_code',
            'tripay_channels',
            'tripay_environment',
        ]);
        // $this->baseUrl = config('payment.tripay.sandbox_base_url');
        if ($this->config->get('tripay_environment') === 'production') {
            $this->baseUrl = config('payment.tripay.base_url');
        } else {
            $this->baseUrl = config('payment.tripay.sandbox_base_url');
        }
    }

    public function updateConfig(array $data)
    {
        foreach ($data as $key => $value) {
            if (! (strpos($key, 'tripay_') !== false)) {
                throw new Exception('Invalid config key');
            }
            if (is_array($value)) {
                $value = implode(',', $value);
            }
            Config::set($key, $value);
        }
    }

    public function validateConfig()
    {
        if (empty($this->config->get('tripay_api_key')) || empty($this->config->get('tripay_private_key')) || empty($this->config->get('tripay_merchant_code'))) {
            throw new AppException('Admin has not yet set up Tripay payment gateway, please tell admin.');
        }
    }

    //doc: https://tripay.co.id/developer?tab=transaction-create
    public function createTransaction(PaymentGateway $trx, Customer $user)
    {
       
        $json = [
            'method'        => $trx['payment_channel'],
            'merchant_ref'  => (string) $trx['id'],
            'amount'        => (int) $trx['price'],
            'customer_name' => $user['fullname'],
            'customer_email'=> $user['email'] ?? 'no-email@example.com',
            'customer_phone'=> $user['phonenumber'],
            'order_items'   => [
                [
                    'sku'        => $trx['plan_id'],
                    'name'       => $trx['plan_name'],
                    'price'      => (int) $trx['price'],
                    'quantity'   => 1,
                ]
            ],
            'return_url'    => route('customer:order.check', $trx),
            'expired_time'  => time() + (24 * 60 * 60), // Expire in 24 hours
            'signature'     => hash_hmac('sha256', $this->config->get('tripay_merchant_code') . $trx['id'] . (int) $trx['price'], $this->config->get('tripay_private_key')),
        ];

        $result = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->config->get('tripay_api_key')
        ])->post($this->baseUrl . '/transaction/create', $json)->collect();

        

        if (! $result->get('success')) {
            throw new AppException('Failed to create transaction: ' . $result->get('message'));
        }

        $trx->gateway_trx_id = $result['data']['reference'];
        $trx->pg_url_payment = $result['data']['checkout_url'];
        $trx->pg_request = json_encode($result);
        $trx->expired_date = date('Y-m-d H:i:s', $result['data']['expired_time']);
        $trx->save();

        return redirect($result['data']['checkout_url']);
    }

    public function getStatus(PaymentGateway $trx, Customer $user)
{
    if ($trx->status == PaymentGatewayStatus::PAID) {
        return true;
    }

    $result = Http::withHeaders([
        'Authorization' => 'Bearer ' . $this->config->get('tripay_api_key')
    ])->get($this->baseUrl . '/transaction/detail', [
        'reference' => $trx->gateway_trx_id
    ])->collect();

    if (! $result->get('success')) {
        throw new AppException('Failed to check transaction status: ' . $result->get('message'));
    }

    $status = $result['data']['status'];
    
    if ($status === 'UNPAID') {
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

        if (in_array($status, ['PAID', 'SUCCESS'])) {
            Package::activatePackage($trx->userRecharge, RechargeGateway::TRIPAY, $result['data']['payment_method'], $trx->transaction_type);

            $trx->pg_paid_response = json_encode($result);
            $trx->payment_method = $result['data']['payment_method'];
            $trx->payment_channel = $result['data']['payment_method'];
            $trx->paid_date = date('Y-m-d H:i:s', strtotime($result['data']['paid_at']));
            $trx->status = PaymentGatewayStatus::PAID;
        } elseif (in_array($status, ['EXPIRED', 'FAILED'])) {
            $trx->pg_paid_response = json_encode($result);
            $trx->status = PaymentGatewayStatus::FAILED;
            throw new AppException('Transaction expired or failed.');
        }

        $trx->save();
        DB::commit();
        return true;

    } catch (Exception $e) {
        DB::rollBack();
        Log::error('Tripay getStatus error: ' . $e->getMessage());
        throw new AppException('Failed to process transaction.');
    }
}

}
