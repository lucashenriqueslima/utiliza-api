<?php

namespace App\Filament\Resources\AuvoCollaboratorResource\Pages;

use App\Filament\Resources\AuvoCollaboratorResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAuvoCollaborators extends ListRecords
{
    protected static string $resource = AuvoCollaboratorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
