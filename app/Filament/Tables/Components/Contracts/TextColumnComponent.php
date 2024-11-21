<?php

namespace App\Filament\Tables\Components\Contracts;

use Filament\Tables\Columns\TextColumn;

interface TextColumnComponent
{
    public static function make(string $name): TextColumn;
}
