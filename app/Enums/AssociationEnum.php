<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum AssociationEnum: string implements HasLabel
{
    case Solidy = 'solidy';
    case Nova = 'nova';
    case Motoclub = 'motoclub';
    case Aprovel = 'aprovel';
    case Agsmb = 'agsmb';

    public function getLabel(): string
    {
        return $this->name;
    }

    public static function getLabelsToCallResource(): array
    {
        return [
            self::Solidy->value => self::Solidy->name,
            self::Motoclub->value => self::Motoclub->name,
            self::Aprovel->value => self::Aprovel->name,
        ];
    }

    public function getDatabaseConnection(): string
    {
        return match ($this) {
            self::Solidy => 'ileva',
            self::Nova => 'ileva_nova',
            self::Motoclub => 'ileva_motoclub',
        };
    }

    public function getLink(): string
    {
        return match ($this) {
            self::Solidy => 'https://solidy.org.br/',
            self::Nova => 'https://novaprotecao.org.br/',
            self::Motoclub => 'https://www.motoclub.org.br',
            self::Aprovel => 'https://www.aaprovel.com.br/',
            self::Agsmb => 'https://agsmb.org.br/',
            default => 'https://www.aaprovel.com.br/',
        };
    }
}
