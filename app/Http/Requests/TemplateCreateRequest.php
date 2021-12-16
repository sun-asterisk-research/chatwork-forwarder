<?php

namespace App\Http\Requests;

use App\Models\Payload;
use App\Rules\ContentMatchPayloadParams;
use Illuminate\Foundation\Http\FormRequest;
use Auth;

class TemplateCreateRequest extends FormRequest
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
            'name' => 'required|string|min:2|max:50|unique:templates,name,NULL,id,user_id,' . Auth::id(),
            'content' => ['required', new ContentMatchPayloadParams($this->params)],
            'params' => 'required',
            'content_type' => 'required|in:'.implode(',', Payload::TYPE),
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Please enter name',
            'name.string' => 'Name is a string',
            'name.min' => 'Name must be at least 2 characters.',
            'name.max' => 'Name is too long (maximum is 50 characters)',
            'name.unique' => 'Name has already been used by another template',
            'content_type.required' => 'Please select content type',
            'content_type.in' => 'Invalid content type',
            'content.required' => 'Please enter content',
            'params.required' => 'Please enter template params',
            'status.required' => 'Please enter status',
        ];
    }
}
