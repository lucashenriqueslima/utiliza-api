<?php

namespace App\Filament\Forms\Components;

use Filament\Forms\Components\TextInput;

class CpfTextInputComponent implements Contracts\TextInputComponent
{
    public static function make(?string $name = null): TextInput
    {
        return TextInput::make($name ?? 'cpf')
            ->label('CPF')
            ->mask('999.999.999-99')
            ->rule('cpf')
            ->unique()
            ->maxLength(14)
            ->required();
    }
}
