<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum BillStatus: string implements HasLabel, HasColor, HasIcon
{
    case Pending = 'pending';
    case Paid = 'paid';
    case Canceled = 'canceled';

    public function getLabel(): string
    {
        return match ($this) {
            self::Pending => 'Pendente',
            self::Paid => 'Pago',
            self::Canceled => 'Cancelado',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Paid => 'success',
            self::Canceled => 'danger',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::Pending => 'heroicon-o-clock',
            self::Paid => 'heroicon-o-check-circle',
            self::Canceled => 'heroicon-o-x-circle',
        };
    }
}
