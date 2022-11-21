<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateAddressRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        if(request()->has('address_type') && request()->input('address_type') === 'shipping') {
            return $this->shippingRules();
        } else {
            return $this->billingRules();
        }
    }

    /**
     * Get billing address validation rules
     * 
     * @return array
     */
    public function billingRules()
    {
        return [
            'billing_address_1' => 'required|max:191',
            'billing_address_zipcode' => 'required|max:191',
            'billing_address_state_id' => 'required|exists:states,id',
            'billing_address_city' => 'required',
        ];
    }

    /**
     * Get shipping address validation rules
     * 
     * @return array
     */
    public function shippingRules()
    {
        return [
            'shipping_address_1' => 'required|max:191',
            'shipping_address_zipcode' => 'required|max:191',
            'shipping_address_state_id' => 'required|exists:states,id',
            'shipping_address_city' => 'required',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        if (request()->has('shipping_address_different')) {
            request()->validate(array_merge(
                $this->rules(),
                $this->shippingRules(),
            ));
        }
    }
}
