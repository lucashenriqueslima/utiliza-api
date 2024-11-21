<?php

namespace App\Filament\Forms\Components;

use Filament\Forms\Components\TextInput;

class EmailTextInputComponent implements Contracts\TextInputComponent
{
    public static function make(?string $name = null): TextInput
    {
        return TextInput::make($name ?? 'email')
            ->label('E-mail')
            ->email()
            ->maxLength(255)
            ->required();
    }
}
