<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;

enum BikerStatus: string implements HasLabel, HasColor, HasIcon
{
    case Avaible = 'avaible';
    case NotAvaible = 'not_avaible';
    case Busy = 'busy';

    public function getLabel(): string
    {
        return match ($this) {
            self::Avaible => 'Disponível',
            self::NotAvaible => 'Indisponível',
            self::Busy => 'Em Serviço',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::NotAvaible => 'danger',
            self::Avaible => 'success',
            self::Busy => 'info',
        };
    }


    public function getIcon(): string
    {
        return match ($this) {
            self::Avaible => 'heroicon-o-magnifying-glass',
            self::Busy => 'heroicon-o-map-pin',
            self::NotAvaible => 'heroicon-o-camera',
        };
    }
}
