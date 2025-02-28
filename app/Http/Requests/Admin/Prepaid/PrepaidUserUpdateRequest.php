<?php

namespace App\Http\Requests\Admin\Prepaid;

use App\Models\Customer;
use App\Models\Plan;
use App\Models\Router;
use App\Models\UserRecharge;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Enum\UpgradeType;

class PrepaidUserUpdateRequest extends FormRequest
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
        return [
            'customer_id' => ['required', Rule::exists(Customer::class, 'id')],
            'upgrade_type' => ['required', Rule::enum(UpgradeType::class)],
            'new_plan_id' => [
                Rule::requiredIf(fn() => $this->input('upgrade_type') !== UpgradeType::DEACTIVATE->value),
                Rule::exists(Plan::class, 'id')
            ],
            'router_id' => ['required', Rule::exists(Router::class, 'id')],
            'plan_id' => ['required', Rule::exists(Plan::class, 'id')],
            'username' => ['required', Rule::exists(UserRecharge::class, 'username')],
            'pppoe_password' => ['required', 'string', 'max:255'],

        ];
    }
}
