<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;

enum CallStatus: string implements HasLabel, HasColor, HasIcon
{
    case SearchingBiker = 'searching_biker';
    case WaitingArrival = 'waiting_arrival';
    case InService = 'in_service';
    case WaitingValidation = 'waiting_validation';
    case Approved = 'approved';

    public function getLabel(): string
    {
        return match ($this) {
            self::SearchingBiker => 'Procurando motoboy',
            self::WaitingArrival => 'Aguardando chegada',
            self::InService => 'Em serviço',
            self::WaitingValidation => 'Aguardando aprovação',
            self::Approved => 'Aprovado',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::SearchingBiker => 'gray',
            self::WaitingArrival => 'warning',
            self::InService => 'info',
            self::WaitingValidation => 'danger',
            self::Approved => 'success',
        };
    }


    public function getIcon(): string
    {
        return match ($this) {
            self::SearchingBiker => 'heroicon-o-magnifying-glass',
            self::WaitingArrival => 'heroicon-o-map-pin',
            self::InService => 'heroicon-o-camera',
            self::WaitingValidation => 'heroicon-o-eye',
            self::Approved => 'heroicon-m-check-badge',
        };
    }
}
