<?php
namespace App\Helpers;

class Helper
{
    public static function indexNumber($page, $perPage, $index)
    {
        $page = $page - 1;

        if ($page < 0) {
            $page = 0;
        }

        return $page * $perPage + $index;
    }
}
