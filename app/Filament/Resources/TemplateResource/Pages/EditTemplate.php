<?php

namespace App\Filament\Resources\TemplateResource\Pages;

use App\Filament\Resources\TemplateResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTemplate extends EditRecord
{
    protected static string $resource = TemplateResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['parameters']  =  json_decode($data['parameters'],true);
        $array = array();

        foreach($data['parameters'] as $param){
            array_push($array,['parameters'=> $param]);
        }

        $data['parameters'] = $array;

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['parameters']  = json_encode(collect($data['parameters'])->pluck('parameters')->toArray());
        return $data;
    }

    
}
