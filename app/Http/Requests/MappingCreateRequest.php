<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MappingCreateRequest extends FormRequest
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
            'keys.*' => 'required|max:100',
            'values.*' => 'required|max:100',
        ];
    }

    /**
     * Get the messages that compatible with validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'keys.*.required' => 'Some key is empty',
            'keys.*.max' => 'Some key is too long(maximum is 100 characters)',
            'values.*.required' => 'Some value is empty',
            'values.*.max' => 'Some value is too long(maximum is 100 characters)',
        ];
    }
}
