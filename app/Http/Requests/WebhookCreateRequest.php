<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WebhookCreateRequest extends FormRequest
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
            'name' => 'required|min:3|max:200|unique:webhooks,name',
            'description' => 'max:1000|',
            'bot_id' => 'required',
            'room_name' => 'required',
            'room_id' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Please enter name',
            'name.min' => 'Name is too short (minimum is 3 characters)',
            'name.max' => 'Name is too long (maximum is 200 characters)',
            'name.unique' => 'This webhook name has already been used by another webhook',
            'description.max' => 'Description is too long (maximum is 1000 characters)',
            'bot_id.required' => 'Please choose chatbot',
            'room_name.required' => 'Please choose room',
            'room_id.required' => 'Please enter room id',
        ];
    }
}
