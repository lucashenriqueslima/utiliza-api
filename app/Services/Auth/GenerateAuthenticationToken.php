<?php

namespace App\Services\Auth;

class GenerateAuthenticationToken
{
    public static function run()
    {
        return rand(1000, 9999);
    }
}