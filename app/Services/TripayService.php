<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TripayService
{
    protected $apiKey;
    protected $privateKey;
    protected $merchantCode;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = env('TRIPAY_API_KEY');
        $this->privateKey = env('TRIPAY_PRIVATE_KEY');
        $this->merchantCode = env('TRIPAY_MERCHANT_CODE');
        $this->baseUrl = env('TRIPAY_MODE') === 'live' ? 'https://tripay.co.id/api/' : 'https://tripay.co.id/api-sandbox/';
    }

    public function getPaymentChannels()
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey
        ])->get($this->baseUrl . 'merchant/payment-channel');

        return $response->json();
    }

    public function createTransaction($payload)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey
        ])->post($this->baseUrl . 'transaction/create', $payload);

        return $response->json();
    }

    public function calculateSignature($reference, $amount)
    {
        return hash_hmac('sha256', $this->merchantCode . $reference . $amount, $this->privateKey);
    }
}
