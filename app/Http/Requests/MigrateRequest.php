<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MigrateRequest extends FormRequest
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
            'url' => 'required|regex:/^(' . str_replace('/', '\/', config('slack.allow_url')) . ')[a-zA-Z0-9]{32}$/i',
        ];
    }

    public function messages()
    {
        return [
            'url.required' => 'URL is required',
            'url.regex' => 'Please use url from Chatwork Forwarder',
        ];
    }
}
