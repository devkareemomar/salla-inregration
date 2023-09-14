<?php

namespace App\Filament\Resources\WorkspaceResource\Pages;

use App\Filament\Resources\WorkspaceResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWorkspaces extends ListRecords
{
    protected static string $resource = WorkspaceResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
