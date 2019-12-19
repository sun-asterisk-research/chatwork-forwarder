<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MappingUpdateRequest extends FormRequest
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
                'max:100',
                Rule::unique('mappings')->ignore($this->mapping->id)->where(function ($query) {
                    return $query->where('webhook_id', $this->webhook->id);
                }),
            ],
            'key' => [
                'required',
                'max:100',
                Rule::unique('mappings')->ignore($this->mapping->id)->where(function ($query) {
                    return $query->where('webhook_id', $this->webhook->id);
                }),
            ],
            'value' => 'required|max:100',
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
            'name.required' => 'Please enter name',
            'name.max' => 'Name is too long(maximum is 100 characters)',
            'name.unique' => 'This mapping name has already been used by another mapping',
            'key.required' => 'Please enter key',
            'key.max' => 'Key is too long(maximum is 100 characters)',
            'key.unique' => 'This mapping key has already been used by another mapping',
            'value.required' => 'Please enter value',
            'value.max' => 'Value is too long(maximum is 100 characters)',
        ];
    }
}
