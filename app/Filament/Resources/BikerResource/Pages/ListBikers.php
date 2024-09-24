<?php

namespace App\Filament\Resources\BikerResource\Pages;

use App\Filament\Resources\BikerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBikers extends ListRecords
{
    protected static string $resource = BikerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
