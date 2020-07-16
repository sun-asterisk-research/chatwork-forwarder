<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserUpdateRequest extends FormRequest
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
        $rules = [
            'name' => 'required|min:5|max:50',
            'email' => [
                'required',
                'max:200',
                Rule::unique('users')->ignore($this->id),
            ],
            'avatar' => 'image|max:5120',
        ];
        if ($this->password != '') {
            $rules['password'] = 'min:8|max:50';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'name.required' => 'Please enter name',
            'name.min' => 'Name is too short (minimum is 5 characters)',
            'name.max' => 'Name is too long (maximum is 50 characters)',
            'email.required' => 'Please enter email',
            'email.unique' => 'This user email has already been used by another user',
            'email.max' => 'Email is too long (maximum is 200 characters)',
            'password.required' => 'Please enter password',
            'password.min' => 'Password is too short (minimum is 8 characters)',
            'password.max' => 'password is too long (maximum is 50 characters)',
            'avatar.image' => 'The avatar is not in the correct format',
            'avatar.max' => 'Maximum file size to upload is 5MB (5120 KB)',
        ];
    }
}
