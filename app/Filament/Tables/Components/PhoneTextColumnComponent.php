<?php

namespace App\Filament\Tables\Components;

use App\Filament\Tables\Components\Contracts\TextColumnComponent;
use App\Helpers\LinkGeneratorHelper;
use Filament\Tables\Columns\TextColumn;

class PhoneTextColumnComponent implements TextColumnComponent
{
    public static function make(string $name = 'phone'): TextColumn
    {
        return TextColumn::make($name)
            ->label('Telefone')
            ->searchable()
            ->sortable();
    }
}
