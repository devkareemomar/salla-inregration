<?php

namespace App\Filament\Resources\MerchantResource\Pages;

use App\Filament\Resources\MerchantResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMerchant extends EditRecord
{
    protected static string $resource = MerchantResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    // protected function mutateFormDataBeforeFill(array $data): array
    // {
    //     dd($data);
    //     $data['parameters']  =  json_decode($data['parameters'],true);
    //     $array = array();

    //     foreach($data['parameters'] as $param){
    //         array_push($array,['parameters'=> $param]);
    //     }

    //     $data['parameters'] = $array;

    //     return $data;
    // }
}
