<?php

namespace App\Http\Requests;

use App\Rules\ConditionFieldMatchPayloadParams;
use Illuminate\Foundation\Http\FormRequest;

class PayloadUpdateRequest extends FormRequest
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
            'content' => 'required',
            'params' => 'required',
            'fields' => new ConditionFieldMatchPayloadParams($this->params),
        ];
    }

    public function messages()
    {
        return [
            'content.required' => 'Please enter content',
            'params.required' => 'Please enter payload params to validate the conditions',
        ];
    }
}
