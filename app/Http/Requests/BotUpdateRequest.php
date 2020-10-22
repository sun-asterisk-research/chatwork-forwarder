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
        $rules = [
            'name' => [
                'required',
                'max:50',
                Rule::unique('bots')->ignore($this->id)->where(function ($query) {
                    return $query->where('user_id', \Auth::id());
                }),
            ],
        ];

        if ($this->bot_key != '') {
            $rules['bot_key'] = [
                'max:100',
                Rule::unique('bots')->ignore($this->id)->where(function ($query) {
                    return $query->where('user_id', \Auth::id());
                }),
            ];
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'name.required' => 'Please enter name',
            'name.max' => 'Name is too long (maximum is 50 characters)',
            'name.unique' => 'This bot name has already been used by another bot',
            'bot_key.max' => 'Bot key is too long (maximum is 100 characters)',
            'bot_key.unique' => 'This bot key has already been used by another bot',
        ];
    }
}
