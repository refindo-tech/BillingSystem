<?php

namespace App\Http\Controllers\Customer;

use App\DataTables\OrderHistoryDataTable;
use App\Enum\PaymentGatewayStatus;
use App\Exceptions\AppException;
use App\Http\Controllers\Controller;
use App\Models\PaymentGateway;
use App\Models\Plan;
use App\Models\Router;
use App\Support\Facades\Config;
use App\Support\Facades\Xendit;
use App\Support\Facades\Tripay;
use App\Models\Customer;

use Illuminate\Support\Collection;

class CustomerOrderController extends Controller
{
    public function index()
    {
        $routers = Router::whereEnabled(true)->get();
        return view('customer.order.list', compact('routers'));
    }

    public function buy(Plan $plan)
    {
        $user = auth()->user();

        if (strpos($user->email, '@') === false) {
            return redirect()->route('customer:profile.edit')->with('error', 'Please enter your email address');
        }

        // Get active payment gateway
        $activeGateway = Config::get('active_payment_gateway');
        //if empty, set default to xendit
        if (empty($activeGateway)) {
            $activeGateway = 'tripay';
        }

        // Validate selected payment gateway config
        if ($activeGateway === 'xendit') {
            Xendit::validateConfig();
        } elseif ($activeGateway === 'tripay') {
            Tripay::validateConfig();
        } else {
            return redirect()->back()->with('error', 'Invalid payment gateway configuration.');
        }

        // Check for existing unpaid transaction
        $order = PaymentGateway::where('username', $user->username)
            ->where('status', PaymentGatewayStatus::UNPAID)
            ->first();

        if ($order && $order->pg_url_payment) {
            return redirect()->route('customer:order.detail', $order)->with('error', 'You already have an unpaid transaction. Please cancel or pay it.');
        }

        if (empty($order)) {
            $order = PaymentGateway::create([
                'username' => $user->username,
                'gateway' => $activeGateway,
                'plan_id' => $plan->id,
                'plan_name' => $plan->name,
                'router_id' => $plan->router->id,
                'router_name' => $plan->router->name,
                'price' => $plan->price,
                'status' => PaymentGatewayStatus::UNPAID,
            ]);
        } else {
            $order->update([
                'username' => $user->username,
                'gateway' => $activeGateway,
                'plan_id' => $plan->id,
                'plan_name' => $plan->name,
                'router_id' => $plan->router->id,
                'router_name' => $plan->router->name,
                'price' => $plan->price,
                'status' => PaymentGatewayStatus::UNPAID,
            ]);
        }

        // Process transaction based on active gateway
        return $activeGateway === 'xendit'
            ? Xendit::createTransaction($order, $user)
            : Tripay::createTransaction($order, $user);
    }

    public function detail(PaymentGateway $order)
    {
        if (empty($order->pg_url_payment)) {
            return redirect()->route('customer:order.buy', $order->plan)->with('error', 'Checking Payment');
        }

        return view('customer.order.detail', compact('order'));
    }

    public function check(PaymentGateway $order)
    {
        $customer = Customer::find(auth()->id()); // This should be a Customer model instance
        $customer = auth()->user(); // This should be a Customer model instance
        if (!$customer instanceof Customer) {
            throw new AppException('Authenticated user is not a customer');
        }

        try {
            if ($order->gateway === 'xendit') {
                Xendit::validateConfig();
                Xendit::getStatus($order, $customer);
                
            } elseif ($order->gateway === 'tripay') {
                Tripay::validateConfig();
                Tripay::getStatus($order, $customer);
            } else {
                throw new AppException('Invalid payment gateway.');
            }

            return redirect()->route('customer:order.detail', $order)->with('success', 'Transaction has been paid');
        } catch (AppException $e) {
            return redirect()->route('customer:order.detail', $order)->with('error', $e->getMessage());
        }
    }


    public function cancel(PaymentGateway $order)
    {
        $order->update([
            'status' => PaymentGatewayStatus::CANCELED,
        ]);

        return redirect()->back()->with('success', 'Transaction has been canceled');
    }

    public function history(OrderHistoryDataTable $dataTable)
    {
        return $dataTable->render('customer.order.history');
    }
}
