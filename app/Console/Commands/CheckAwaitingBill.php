<?php

namespace App\Console\Commands;

use App\Enum\PlanType;
use App\Enum\PaymentGatewayStatus;
use App\Enum\PendingUserRechargeStatus;
use App\Models\UserRecharge;
use App\Models\PendingUserRecharge;
use App\Models\PaymentGateway;
use App\Support\Mikrotik;
use App\Support\Package;
use App\Support\Facades\Config;
use Illuminate\Console\Command;

class CheckAwaitingBill extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-awaiting-bill';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Memeriksa tagihan bulanan yang belum dibayar';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Starting the check-awaiting-bill command...");

        // Get user recharges that are about to expire in the next 7 days
        $userRecharges = UserRecharge::where('status', 'on')
            ->whereDate('expired_at', '<=', now()->addDays(7))
            ->get();
        
        foreach ($userRecharges as $userRecharge) {
            $this->info("Checking user: {$userRecharge->customer->username}...");
            
            // Check if there is a pending user recharge
            $awaitingBill = PendingUserRecharge::where('user_recharge_id', $userRecharge->id)
                ->where('status', PendingUserRechargeStatus::WAITING)
                ->first();
            
            if ($awaitingBill) {
                $this->info("Pending user recharge found for {$userRecharge->customer->username}. Processing...");
                
                $this->processPayment($awaitingBill);
            } else {
                $this->info("No pending user recharge found for {$userRecharge->customer->username}. Creating new payment gateway...");
                
                $activeGateway = Config::get('active_payment_gateway', 'tripay');
                $payment_channel = explode(' - ', $userRecharge->method)[1] ?? 'default';
                
                $paymentGateway = PaymentGateway::create([
                    'username' => $userRecharge->customer->username,
                    'user_recharge_id' => $userRecharge->id,
                    'gateway' => $activeGateway,
                    'plan_id' => $userRecharge->plan_id,
                    'plan_name' => $userRecharge->plan->name,
                    'router_id' => $userRecharge->router_id,
                    'router_name' => $userRecharge->router->name,
                    'price' => $userRecharge->plan->price,
                    'status' => PaymentGatewayStatus::UNPAID,
                    'payment_channel' => $payment_channel,
                    'transaction_type' => 'recharge',
                ]);
                
                $this->info("Payment gateway created for {$userRecharge->customer->username}.");
                Package::createInvoice($userRecharge, $activeGateway, $payment_channel);
                $this->info("Invoice created for {$userRecharge->customer->username}.");
                $this->processTransaction($paymentGateway, $userRecharge->customer, $activeGateway);
            }
        }
    }

    private function processPayment(PendingUserRecharge $awaitingBill)
    {
        $paymentGateway = PaymentGateway::where('user_recharge_id', $awaitingBill->user_recharge_id)
            ->where('status', PaymentGatewayStatus::UNPAID)
            ->first();

        if ($paymentGateway) {
            $this->info("Payment gateway already exists for {$awaitingBill->username}. Skipping...");
        } else {
            $this->info("Creating a new payment gateway for {$awaitingBill->username}...");
            
            $activeGateway = Config::get('active_payment_gateway', 'tripay');
            $payment_channel = explode(' - ', $awaitingBill->userRecharge->method)[1] ?? 'default';
            
            $paymentGateway = PaymentGateway::create([
                'username' => $awaitingBill->userRecharge->customer->username,
                'user_recharge_id' => $awaitingBill->user_recharge_id,
                'gateway' => $activeGateway,
                'plan_id' => $awaitingBill->plan_id,
                'plan_name' => $awaitingBill->plan->name,
                'router_id' => $awaitingBill->router_id,
                'router_name' => $awaitingBill->router->name,
                'price' => $awaitingBill->price,
                'status' => PaymentGatewayStatus::UNPAID,
                'payment_channel' => $payment_channel,
                'transaction_type' => 'recharge',
            ]);
            
            $this->info("Payment gateway created for {$awaitingBill->username}.");
            Package::createInvoice($awaitingBill->userRecharge, $activeGateway, $payment_channel);
            $this->info("Invoice created for {$awaitingBill->username}.");
            $this->processTransaction($paymentGateway, $awaitingBill->userRecharge->customer, $activeGateway);

            $awaitingBill->update(['status' => PendingUserRechargeStatus::CONFIRMED]);
        }
    }

    private function processTransaction(PaymentGateway $paymentGateway, $customer, $activeGateway)
    {
        $this->info("Processing transaction for {$customer->username} using {$activeGateway}...");

        if ($activeGateway === 'xendit') {
            $xendit = new \App\Repository\PaymentXenditRepository();
            $xendit->createTransaction($paymentGateway, $customer);
        } elseif ($activeGateway === 'tripay') {
            $tripay = new \App\Repository\PaymentTriPayRepository();
            $tripay->createTransaction($paymentGateway, $customer);
        } else {
            $this->error("Invalid payment gateway configuration for {$customer->username}.");
        }
    }
}
