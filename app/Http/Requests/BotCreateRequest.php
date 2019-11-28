<?php

namespace App\Http\Requests;

use Auth;
use Illuminate\Foundation\Http\FormRequest;

class BotCreateRequest extends FormRequest
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
            'name' => 'required|min:1|max:50|unique:bots,name,NULL,id,user_id,' . Auth::id(),
            'cw_id' => 'required|min:1|max:10|unique:bots,cw_id,NULL,id,user_id,' . Auth::id(),
            'bot_key' => 'required|min:1|max:50|unique:bots,bot_key,NULL,id,user_id,' . Auth::id(),
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Please enter name',
            'name.min' => 'Name is too short (minimum is 1 characters)',
            'name.max' => 'Name is too long (maximum is 50 characters)',
            'name.unique' => 'This bot name has already been used by another bot',
            'cw_id.required' => 'Please enter chatwork bot id',
            'cw_id.min' => 'Chatwork bot id is too short (minimum is 1 characters)',
            'cw_id.max' => 'Chatwork bot id is too long (maximum is 10 characters)',
            'cw_id.unique' => 'This chat work bot id has already been used by another bot',
            'bot_key.required' => 'Please enter bot key',
            'bot_key.min' => 'Bot key is too short (minimum is 1 characters)',
            'bot_key.max' => 'Bot key is too long (maximum is 50 characters)',
            'bot_key.unique' => 'This bot key has already been used by another bot',
        ];
    }
}
