<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExecuteCheckoutRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'payment_method' => ['required', 'in:paypal,credit_card'],
            'shipping_id' => ['required'],
            'cc_name' => ['nullable', 'required_if:payment_method,credit_card'],
            'cc_number' => ['nullable', 'required_if:payment_method,credit_card'],
            'cc_expiration_month' => ['nullable', 'required_if:payment_method,credit_card', 'numeric', 'between:01,12'],
            'cc_expiration_year' => ['nullable', 'required_if:payment_method,credit_card', 'numeric', 'between:20,30'],
            'cc_cvv' => ['nullable', 'required_if:payment_method,credit_card'],
        ];
    }
}
