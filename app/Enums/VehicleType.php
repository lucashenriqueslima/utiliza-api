<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum VehicleType: string implements HasLabel
{
    case Car = 'car';
    case Motorcycle = 'motorcycle';

    public function getLabel(): string
    {
        return match ($this) {
            self::Car => 'Carro',
            self::Motorcycle => 'Moto',
        };
    }
}
