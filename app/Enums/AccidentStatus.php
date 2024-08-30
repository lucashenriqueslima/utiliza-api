<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;

enum AccidentStatus: string implements HasLabel, HasColor, HasIcon
{
    case Pending = 'pending';
    case InProgress = 'in_progress';
    case Finished = 'finished';

    public function getLabel(): string
    {
        return match ($this) {
            self::Pending => 'Pendente',
            self::InProgress => 'Em andamento',
            self::Finished => 'Finalizado',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Pending => 'danger',
            self::InProgress => 'info',
            self::Finished => 'success',
        };
    }


    public function getIcon(): string
    {
        return match ($this) {
            self::Pending => 'heroicon-o-exclamation-triangle',
            self::InProgress => 'heroicon-o-camera',
            self::Finished => 'heroicon-o-check',
        };
    }
}
