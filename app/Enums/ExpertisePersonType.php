<?php

namespace App\Enums;

enum ExpertisePersonType: string
{
    case Associate = 'associate';
    case ThirdParty = 'third_party';

    public function getLabel(): string
    {
        return match ($this) {
            self::Associate => 'Associado',
            self::ThirdParty => 'Terceiro',
        };
    }
}
