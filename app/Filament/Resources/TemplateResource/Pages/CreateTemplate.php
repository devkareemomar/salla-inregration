<?php

namespace App\Filament\Resources\TemplateResource\Pages;

use App\Filament\Resources\TemplateResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateTemplate extends CreateRecord
{
    protected static string $resource = TemplateResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
    
        $data['parameters']  = json_encode(collect($data['parameters'])->pluck('parameters')->toArray());
        return $data;
    }
    

}
