<?php

namespace App\Services;

use App\Models\ThirdParty;
use App\Models\ThirdPartyCar;

class ThirdPartyCarService
{
    public static function create(ThirdParty $thirdParty, string $plate): void
    {
        $thirdParty->car()->create([
            'plate' => $plate,
        ]);
    }
}
