<?php

namespace App\Http\Requests;

use Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BotUpdateRequest extends FormRequest
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
            'name' => [
                'required',
                'max:50',
                Rule::unique('bots')->ignore($this->id)->where(function ($query) {
                    return $query->where('user_id', \Auth::id());
                }),
            ],
            'bot_key' => [
                'required',
                'max:50',
                Rule::unique('bots')->ignore($this->id)->where(function ($query) {
                    return $query->where('user_id', \Auth::id());
                }),
            ],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Please enter name',
            'name.max' => 'Name is too long (maximum is 50 characters)',
            'name.unique' => 'This bot name has already been used by another bot',
            'bot_key.required' => 'Please enter bot key',
            'bot_key.max' => 'Bot key is too long (maximum is 50 characters)',
            'bot_key.unique' => 'This bot key has already been used by another bot',
        ];
    }
}
