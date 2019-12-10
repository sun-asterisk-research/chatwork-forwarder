<?php

namespace App\Rules;

use ErrorException;
use Illuminate\Contracts\Validation\Rule;
use Throwable;

class ConditionFieldMatchPayloadParams implements Rule
{
    private $errors;
    private $payloadParams;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($payloadParams)
    {
        $this->payloadParams = $payloadParams;
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
        $payload = json_decode($this->payloadParams);
        for ($i = 0; $i < count($value); $i++) {
            try {
                eval('return ' . $value[$i] . ';');
            } catch (Throwable | ErrorException $err) {
                $this->errors['field' . $i] = 'This field is not match with params';
                continue;
            }
        }
        return empty($this->errors) ? true : false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return ['fields' => $this->errors];
    }
}
