<?php

namespace App\Console\Commands;

use App\Enum\PlanType;
use App\Enum\PaymentGatewayStatus;
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


        // search for expired services
        $awaitingBills = PendingUserRecharge::where('status', 'waiting')->whereDate('scheduled_for', '<=', now())->get();

        $this->info("Found {$awaitingBills->count()} awaiting bills.");
        foreach ($awaitingBills as $awaitingBill) {
            $this->info("Processing awaiting bill for {$awaitingBill->username}...");

            // check if the user has a payment gateway
            $paymentGateway = PaymentGateway::where('user_recharge_id', $awaitingBill->user_recharge_id)->where('status', PaymentGatewayStatus::UNPAID)->first();
            if ($paymentGateway) {
                //skip if there's already a payment gateway
                $this->info("Payment gateway found for {$awaitingBill->username}. Skipping...");
            } else {
                //create user transaction
                $this->info("Payment gateway not found for {$awaitingBill->username}. Creating a new payment gateway...");
                $activeGateway = Config::get('active_payment_gateway');
                if (empty($activeGateway)) {
                    $activeGateway = 'tripay';
                }

                $customer = $awaitingBill->userRecharge->customer;
                $payment_channel = $awaitingBill->userRecharge->method;
                $payment_channel = explode(' - ', $payment_channel)[1];
                $paymentGateway = PaymentGateway::create([
                    'username' => $customer->username,
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
                $this->info("Processing transaction for {$awaitingBill->username}...");

                // Process transaction based on active gateway
                if ($activeGateway === 'xendit') {
                    $this->info("Processing transaction for {$awaitingBill->username} using Xendit...");
                    $xendit = new \App\Repository\PaymentXenditRepository();
                    $xendit->createTransaction($paymentGateway, $customer);
                } elseif ($activeGateway === 'tripay') {
                    $this->info("Processing transaction for {$awaitingBill->username} using TriPay...");
                    $tripay = new \App\Repository\PaymentTriPayRepository();
                    $tripay->createTransaction($paymentGateway, $customer);
                } else {
                    $this->error("Invalid payment gateway configuration for {$awaitingBill->username}.");
                }

            }
        }

    }
}
