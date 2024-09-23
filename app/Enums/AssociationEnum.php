<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum AssociationEnum: string implements HasLabel
{
    case Solidy = 'solidy';
    case Nova = 'nova';
    case Motoclub = 'motoclub';

    public function getLabel(): string
    {
        return $this->name;
    }

    public function getDatabaseConnection(): string
    {
        return match ($this) {
            self::Solidy => 'ileva',
            self::Nova => 'ileva_nova',
            self::Motoclub => 'ileva_motoclub',
        };
    }
}
