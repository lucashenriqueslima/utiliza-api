<?php

namespace App\Filament\Resources\AccidentResource\Pages;

use App\Filament\Resources\AccidentResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAccident extends CreateRecord
{
    protected static string $resource = AccidentResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
