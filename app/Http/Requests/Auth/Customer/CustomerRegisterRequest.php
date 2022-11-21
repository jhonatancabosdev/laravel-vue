<?php

namespace App\Http\Requests\Auth\Customer;

use Illuminate\Foundation\Http\FormRequest;

class CustomerRegisterRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:119',
            'email' => 'required|string|email|max:199|unique:customers',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'required|min:8',
        ];
    }
}
