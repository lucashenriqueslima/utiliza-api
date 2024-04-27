<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasColor;

enum CallRequestStatus: string implements HasLabel, HasColor
{
    case Denied = 'denied';
    case Accepted = 'accepted';
    case NotAnsewered = 'not_answered';

    public function getLabel(): string
    {
        return match ($this) {
            self::Denied => 'Negado',
            self::NotAnsewered => 'Não Respondido',
            self::Accepted => 'Aceito',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Denied => 'danger',
            self::NotAnsewered => 'info',
            self::Accepted => 'success',
        };
    }
}
    