<?php

namespace App\Filament\Resources\CallResource\Pages;

use App\Filament\Resources\CallResource;
use App\Filament\Resources\CallResource\Widgets\BikersAvaibleWidget;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCalls extends ListRecords
{
    protected static string $resource = CallResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            BikersAvaibleWidget::class,
        ];
    }
}
