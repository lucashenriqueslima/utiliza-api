<?php

namespace App\Filament\Forms\Components\Contracts;

use Filament\Forms\Components\TextInput;

interface TextInputComponent
{
    public static function make(?string $name = null): TextInput;
}
