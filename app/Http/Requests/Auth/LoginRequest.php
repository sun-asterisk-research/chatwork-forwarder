<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => 'required|email|max:100',
            'password' => 'required|min:8|max:20',
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'Please enter email',
            'email.email' => 'Email must be formatted as local-part@domain',
            'email.max' => 'Email is too long (maximum is 100 characters)',
            'password.required' => 'Please enter password',
            'password.min' => 'Password is too short (minimum is 8 characters)',
            'password.max' => 'Password is too long (maximum is 20 characters)',
        ];
    }
}
