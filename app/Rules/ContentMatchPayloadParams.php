<?php

namespace App\Rules;

use ErrorException;
use Illuminate\Contracts\Validation\Rule;
use Throwable;

class ContentMatchPayloadParams implements Rule
{
    private $errorFields;
    private $payloadParams;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($payloadParams)
    {
        $this->errorFields = [];
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
        $value = $this->getStringsBetweebBrackets($value);
        $params = json_decode($this->payloadParams);
        for ($i = 0; $i < count($value); $i++) {
            try {
                eval('return ' . $value[$i] . ';');
            } catch (Throwable | ErrorException $err) {
                array_push($this->errorFields, $value[$i]);
                continue;
            }
        }

        return empty($this->errorFields) ? true : false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return join(', ', $this->errorFields) . ' not found in payload params';
    }

    private function getStringsBetweebBrackets($str)
    {
        $regex = '#{{(.*?)}}#';
        preg_match_all($regex, $str, $matches);

        return $matches[1];
    }
}
