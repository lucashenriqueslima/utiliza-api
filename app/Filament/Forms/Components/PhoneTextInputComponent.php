<?php

namespace App\Filament\Forms\Components;

use Filament\Forms\Components\TextInput;

class PhoneTextInputComponent implements Contracts\TextInputComponent
{
    public static function make(?string $name = null): TextInput
    {
        return TextInput::make($name ?? 'phone')
            ->label('Telefone')
            ->mask('(99) 99999-9999')
            ->maxLength(15)
            ->required();
    }
}
