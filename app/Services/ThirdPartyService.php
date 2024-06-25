<?php

namespace App\Services;

use App\Models\Expertise;
use App\Models\ThirdParty;

class ThirdPartyService
{
    public static function create(Expertise $expertise, array $data): ThirdParty
    {
        $expertise->thirdParty()->create([
            'name' => $data['name'],
            'cpf' => $data['cpf'],
            'phone' => $data['phone'],
        ]);

        return $expertise->thirdParty;
    }
}
