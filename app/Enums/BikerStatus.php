<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum BikerStatus: string 
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
}
    