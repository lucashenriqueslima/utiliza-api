<?php

namespace App\Filament\Resources\AuvoCollaboratorResource\Pages;

use App\Filament\Resources\AuvoCollaboratorResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAuvoCollaborator extends EditRecord
{
    protected static string $resource = AuvoCollaboratorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
