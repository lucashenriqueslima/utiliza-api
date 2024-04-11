<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;

enum CallStatus: string implements HasLabel, HasColor, HasIcon
{
    case WaitingBiker = 'waiting_biker';
    case InService = 'in_service';
    case Done = 'done';

    public function getLabel(): string
    {
        return match ($this) {
            self::WaitingBiker => 'Buscando Motoboy',
            self::InService => 'Em Serviço',
            self::Done => 'Concluído',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::WaitingBiker => 'warning',
            self::InService => 'info',
            self::Done => 'success',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::WaitingBiker => 'heroicon-o-magnifying-glass',
            self::InService => 'heroicon-m-arrow-path',
            self::Done => 'heroicon-m-check-badge',
        };
    }
}
    