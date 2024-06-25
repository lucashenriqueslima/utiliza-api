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
    case InValidation = 'in_validation';
    case WaitingBikerSeeValidation = 'waiting_biker_see_validation';
    case Approved = 'approved';

    public function getLabel(): string
    {
        return match ($this) {
            self::SearchingBiker => 'Procurando motoboy',
            self::WaitingArrival => 'Aguardando chegada',
            self::InService => 'Em serviço',
            self::WaitingValidation => 'Aguardando validação',
            self::InValidation => 'Em validação',
            self::WaitingBikerSeeValidation => 'Aguardando motoboy ver validação',
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
            self::InValidation => 'info',
            self::WaitingBikerSeeValidation => 'info',
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
            self::InValidation => 'heroicon-o-eye',
            self::WaitingBikerSeeValidation => 'heroicon-o-eye',
            self::Approved => 'heroicon-m-check-badge',
        };
    }
}
