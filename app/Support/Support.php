<?php
namespace App\Support;

trait Support
{
    public function getValues($data, $field)
    {
        $attributes = explode('.', trim($field));
        if ($attributes[0] === '$params') {
            array_shift($attributes);
        }
        $currentValue = $data;
        foreach ($attributes as $attr) {
            $currentValue = $currentValue[$attr];
        }

        return $currentValue;
    }
}
