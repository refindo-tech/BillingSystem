<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TripayService;

class TripayController extends Controller
{
    protected $tripay;

    public function __construct(TripayService $tripay)
    {
        $this->tripay = $tripay;
    }

    public function showPaymentChannels()
    {
        $channels = $this->tripay->getPaymentChannels();
        // return view('tripay.channels', ['channels' => $channels]);
    }

    public function createTransaction(Request $request)
    {
        $payload = [
            'method'         => $request->method, 
            'merchant_ref'   => 'INV-'.time(),
            'amount'         => $request->amount,
            'customer_name'  => $request->customer_name,
            'customer_email' => $request->customer_email,
            'order_items'    => $request->order_items,
            'return_url'     => url('/transaction/success'),
            'expired_time'   => (time() + (24 * 60 * 60)), // 24 hours
        ];

        $signature = $this->tripay->calculateSignature($payload['merchant_ref'], $payload['amount']);
        $payload['signature'] = $signature;

        $transaction = $this->tripay->createTransaction($payload);
        return response()->json($transaction);
    }
}
