<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubscribeRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => 'email|required|unique:subscribers'
        ];
    }

    /**
     * Validation message
     * 
     * @return array
     */
    public function messages()
    {
        return [
            'email.unique' => "You're already in our database!",
        ];
    }
}
