<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PaymentGateway;
use App\Enum\PaymentGatewayStatus;
use App\Support\Package;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Exceptions\AppException;
use App\Enum\RechargeGateway;
use App\Support\Facades\Config;

class WebhookController extends Controller
{
    /**
     * Handle Tripay webhook callback.
     */
    public function handleTripay(Request $request)
    {
        
        
        Log::info('Tripay callback received', $request->all());

        $signatureKey = Config::get('tripay_private_key');

        $json = file_get_contents('php://input');
        $signature = hash_hmac('sha256', $json, $signatureKey);

        if ($request->header('X-Callback-Signature') !== $signature) {
            return response()->json(['success' => false, 'message' => 'Invalid signature'], 403);
        }


        $data = $request->all();


        // Find transaction by reference ID and lock row
        $trx = PaymentGateway::where('gateway_trx_id', $data['reference'])->lockForUpdate()->first();
        if (! $trx) {
            return response()->json(['success' => false, 'message' => 'Transaction not found'], 404);
        }

        // Prevent duplicate processing
        if ($trx->status == PaymentGatewayStatus::PAID) {
            return response()->json(['success' => true, 'message' => 'Transaction already processed']);
        }

        DB::beginTransaction();
        try {
            if (in_array($data['status'], ['PAID', 'SUCCESS'])) {
                Package::activatePackage(
                    $trx->userRecharge, 
                    RechargeGateway::TRIPAY, 
                    $data['payment_method'], 
                    $trx->transaction_type
                );

                $trx->pg_paid_response = json_encode($data);
                $trx->payment_method = $data['payment_method_code'];
                $trx->payment_channel = $data['payment_method_code'];
                $trx->paid_date = now();
                $trx->status = PaymentGatewayStatus::PAID;
            } elseif (in_array($data['status'], ['EXPIRED', 'FAILED'])) {
                $trx->pg_paid_response = json_encode($data);
                $trx->status = PaymentGatewayStatus::FAILED;
            }
            
            $trx->save();
            DB::commit();
            return response()->json(['success' => true]);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Tripay webhook processing error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Processing error'], 500);
        }
    }

    /**
     * Handle Xendit webhook callback.
     */
    public function handleXendit(Request $request)
    {
        Log::info('Xendit callback received', $request->all());

        $data = $request->all();

        // Find transaction by gateway ID and lock row
        $trx = PaymentGateway::where('gateway_trx_id', $data['id'])->lockForUpdate()->first();

        if (! $trx) {
            return response()->json(['success' => false, 'message' => 'Transaction not found'], 404);
        }

        // Prevent duplicate processing
        if ($trx->status == PaymentGatewayStatus::PAID) {
            return response()->json(['success' => true, 'message' => 'Transaction already processed']);
        }

        DB::beginTransaction();
        try {
            if (in_array($data['status'], ['PAID', 'SETTLED'])) {
                Package::activatePackage(
                    $trx->userRecharge, 
                    RechargeGateway::XENDIT, 
                    $data['payment_channel'], 
                    $trx->transaction_type
                );

                $trx->pg_paid_response = json_encode($data);
                $trx->payment_method = $data['payment_method'] ?? null;
                $trx->payment_channel = $data['payment_channel'];
                $trx->paid_date = now();
                $trx->status = PaymentGatewayStatus::PAID;
            } elseif ($data['status'] === 'EXPIRED') {
                $trx->pg_paid_response = json_encode($data);
                $trx->status = PaymentGatewayStatus::FAILED;
            }

            $trx->save();
            DB::commit();
            return response()->json(['success' => true]);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Xendit webhook processing error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Processing error'], 500);
        }
    }
}
