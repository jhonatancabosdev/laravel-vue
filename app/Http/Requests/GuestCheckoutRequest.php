<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GuestCheckoutRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['required', 'max:191'],
            'email' => ['required', 'email:rfc,dns', 'max:191'],
            'phone' => ['required', 'between:9,10', 'max:191'],
            'billing_address_1' => ['required', 'max:191'],
            'billing_address_zipcode' => ['required', 'exists:zipcodes,name', 'max:191'],
            'billing_address_state_id' => ['required', 'exists:states,id'],
            'billing_address_city' => ['required', 'max:20'],
            'shipping_address_different' => ['nullable', 'in:true,false'],
            'shipping_address_1' => ['required_if:shipping_address_different,true', 'nullable', 'max:191'],
            'shipping_address_zipcode' => ['required_if:shipping_address_different,true', 'nullable', 'exists:zipcodes,name', 'max:191'],
            'shipping_address_state_id' => ['required_if:shipping_address_different,true', 'nullable', 'exists:states,id'],
            'shipping_address_city' => ['required_if:shipping_address_different,true', 'nullable', 'max:20'],
        ];
    }
}