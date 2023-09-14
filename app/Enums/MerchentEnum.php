<?php


namespace App\Enums;

use App\Enums\EnumUtils;
use App\Models\Merchant;

class MerchentEnum
{
    use EnumUtils;

    public static function merchants()
    {
        foreach (Merchant::select('name','merchant','id')->get() as $merchant){
                $result[$merchant->id] = $merchant->name .'('.$merchant->merchant.')'; 
        }
        return $result;
    }
}
