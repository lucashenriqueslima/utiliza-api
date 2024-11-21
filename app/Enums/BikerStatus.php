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
    case Banned = 'banned';
    case Inactive = 'inactive';
    case PendingAuthentication = 'pending_authenticator';

    public function getLabel(): string
    {
        return match ($this) {
            self::Avaible => 'Disponível',
            self::NotAvaible => 'Indisponível',
            self::Busy => 'Em Serviço',
            self::Banned => 'Banido',
            self::Inactive => 'Inativo',
            self::PendingAuthentication => 'Pendente Autenticação',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::NotAvaible => 'danger',
            self::Avaible => 'success',
            self::Busy => 'info',
            self::Banned => 'danger',
            self::Inactive => 'danger',
            self::PendingAuthentication => 'warning',
        };
    }


    public function getIcon(): string
    {
        return match ($this) {
            self::Avaible => 'heroicon-o-magnifying-glass',
            self::Busy => 'heroicon-o-map-pin',
            self::NotAvaible => 'heroicon-o-camera',
            self::Banned => 'heroicon-o-exclamation-circle',
            self::Inactive => 'heroicon-o-exclamation-circle',
            self::PendingAuthentication => 'heroicon-o-clock',
        };
    }
}
