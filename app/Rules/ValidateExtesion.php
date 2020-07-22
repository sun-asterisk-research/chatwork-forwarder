<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\UploadedFile;

class ValidateExtesion implements Rule
{
    protected $file;

    protected $type;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(UploadedFile $file, $type)
    {
        $this->file = $file;
        $this->type = $type;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return $this->file->getClientOriginalExtension() === $this->type;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return "The file must be a file of type: {$this->type}";
    }
}
