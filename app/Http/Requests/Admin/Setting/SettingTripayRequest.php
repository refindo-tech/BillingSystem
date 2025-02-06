<?php

namespace App\Http\Requests\Admin\Setting;

use Illuminate\Foundation\Http\FormRequest;

class SettingTripayRequest extends FormRequest
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
            'tripay_api_key'=> 'required|string',
            'tripay_private_key'=> 'required|string',
            'tripay_merchant_code'=> 'required|string',
            'tripay_channels' => 'nullable|array',
        ];
    }
}
