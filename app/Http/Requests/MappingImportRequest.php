<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MappingImportRequest extends FormRequest
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
        $size = config('validation.validation.max_size_1m');

        return [
            'file' => "required|file|mimetypes:application/json|max:$size",
        ];
    }
}
