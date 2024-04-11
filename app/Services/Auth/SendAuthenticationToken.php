<?php

namespace App\Services\Auth;

use App\Models\Locavibe\LocavibeRenter;
use App\Notifications\AuthenticationTokenNotification;

class SendAuthenticationToken
{
    public static function run(LocavibeRenter $renter, string $token)
    {

        $renter->notify(new AuthenticationTokenNotification($token));
    }


}