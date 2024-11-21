<?php

namespace App\Filament\Forms\Components;

use Filament\Forms\Components\TextInput;

class NameTextInputComponent implements Contracts\TextInputComponent
{
    public static function make(?string $name = null): TextInput
    {
        return TextInput::make($name ?? 'name')
            ->label('Nome')
            ->maxLength(255)
            ->required();
    }
}
