<?php

namespace App\Enums;

use BackedEnum;

trait EnumUtils
{
    /** Get an array of case values. */
    public static function values(): array
    {
        $array = static::events();

        foreach (array_keys($array) as $val){
            $values[$val] = $val;
        }

        return $values;
    }
    public static function parameters($key): array
    {
        $array = static::events();

        return $array[$key] ?? array();
    }
    
}
