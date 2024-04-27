<?php

namespace App\Enums;

enum VehicleType: string 
{
    case Car = 'carro';
    case Motorcycle = 'moto';
    case Truck = 'caminhao';
    case Other = 'outros';

    public function getLabel(): string
    {
        return match ($this) {
            self::Car => 'Carro',
            self::Motorcycle => 'Moto',
            self::Truck => 'Caminhão',
            self::Other => 'Outro',
        };
    }
}
    