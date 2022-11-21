<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class ConfirmCheckoutRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'payment_method' => ['required', Rule::in(['paypal', 'credit_card'])],
            'courier' => 'required|exists:couriers,id'
        ];
    }

    /**
     * @return array
     */
    public function messages()
    {
        return [
            'courier.required' => 'Please choose a valid shipping option.',
            'courier.exists' => 'The selected shipping option is invalid.'
        ];
    }
}
