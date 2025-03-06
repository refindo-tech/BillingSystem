<?php

namespace App\Http\Requests\Admin\Prepaid;

use App\Models\Customer;
use App\Models\Plan;
use App\Models\Router;
use App\Models\UserRecharge;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use App\Support\Facades\Config;

class PrepaidUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {

        $activeGateway = Config::get('active_payment_gateway') ?? 'xendit';
        $paymentChannel = Config::get($activeGateway . '_channels');
        $paymentChannel = explode(',', $paymentChannel);

        return [
            'customer_id' => ['required', Rule::exists(Customer::class, 'id')],
            'router_id' => ['required', Rule::exists(Router::class, 'id')],
            'plan_id' => ['required', Rule::exists(Plan::class, 'id')],
            'username' => ['required', Rule::unique(UserRecharge::class, 'username')],
            'pppoe_password' => ['required', 'string', 'max:255'],
            // 'payment_channel' => ['required', 'string', 'max:255'],
            'payment_channel' => ['required', Rule::in($paymentChannel)],

        ];
    }
}
