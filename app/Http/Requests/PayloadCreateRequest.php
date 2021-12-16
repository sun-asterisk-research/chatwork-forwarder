<?php

namespace App\Http\Requests;

use App\Models\Payload;
use App\Rules\ConditionFieldMatchPayloadParams;
use App\Rules\ContentMatchPayloadParams;
use Illuminate\Foundation\Http\FormRequest;

class PayloadCreateRequest extends FormRequest
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
            'content' => ['required', new ContentMatchPayloadParams($this->params)],
            'params' => 'required',
            'fields' => new ConditionFieldMatchPayloadParams($this->params),
            'content_type' => 'required|in:'.implode(',', Payload::TYPE),
        ];
    }

    public function messages()
    {
        return [
            'content.required' => 'Please enter content',
            'params.required' => 'Please enter payload params to validate the conditions',
            'content_type.required' => 'Please select content type',
            'content_type.in' => 'Invalid content type',
        ];
    }
}
