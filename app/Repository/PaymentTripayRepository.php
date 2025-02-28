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

class PaymentTripayRepository
{
    protected string $baseUrl;
    protected Collection $config;

    public function __construct()
    {
        $this->baseUrl = config('payment.tripay.base_url');
        $this->config = Config::all()->only([
            'tripay_api_key',
            'tripay_private_key',
            'tripay_merchant_code',
            'tripay_channels',
        ]);
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
            'method'        => $trx->payment_channel, // Tripay's payment method (e.g., BRIVA, QRIS, etc.)
            'merchant_ref'  => (string) $trx['id'],
            'amount'        => (int) $trx['price'],
            'customer_name' => $user['name'],
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

        if (in_array($status, ['PAID', 'SUCCESS']) && $trx->status != PaymentGatewayStatus::PAID) {
            try {
                Package::activatePackage($trx->userRecharge, RechargeGateway::TRIPAY, $result['payment_channel']);
            } catch (Exception $e) {
                throw new AppException('Failed to activate your package, please try again.');
            }

            $trx->pg_paid_response = json_encode($result);
            $trx->payment_method = $result['data']['payment_method'];
            $trx->payment_channel = $result['data']['payment_name'];
            $trx->paid_date = date('Y-m-d H:i:s', strtotime($result['data']['pay_time']));
            $trx->status = PaymentGatewayStatus::PAID;
            $trx->save();

            return true;
        }

        if ($status === 'EXPIRED' || $status === 'FAILED') {
            $trx->pg_paid_response = json_encode($result);
            $trx->status = PaymentGatewayStatus::FAILED;
            $trx->save();
            throw new AppException('Transaction expired or failed.');
        }

        throw new AppException('Unknown transaction status.');
    }
}
