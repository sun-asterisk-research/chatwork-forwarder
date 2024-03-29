<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Auth;

class WebhookUpdateRequest extends FormRequest
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
            'description' => 'max:1000',
            'bot_id' => 'required_without:use_default',
            'use_default' => 'required_if:bot_id,null|in:on',
            'room_name' => 'required',
            'room_id' => 'required',
            'name' => [
                'required',
                'max:50',
                'min:3',
                Rule::unique('webhooks')->ignore($this->id)->where(function ($query) {
                    return $query->where('user_id', \Auth::id());
                }),
            ],
        ];
    }
    public function messages()
    {
        return [
            'name.min' => 'Name is too short (minimum is 3 characters)',
            'name.required' => 'Please enter name',
            'name.max' => 'Name is too long (maximum is 50 characters)',
            'name.unique' => 'This webhook name has already been used by another webhook',
            'use_default.required_if' => 'Default option is required',
            'use_default.in' => 'Invalid default option',
            'description.max' => 'Description is too long (maximum is 1000 characters)',
            'bot_id.required_without' => 'Please choose chatbot',
            'room_name.required' => 'Please choose room',
            'room_id.required' => 'Please enter room id',
        ];
    }
}
