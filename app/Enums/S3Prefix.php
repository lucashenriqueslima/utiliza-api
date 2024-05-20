<?php

namespace App\Enums;

enum S3Prefix: string
{    case Expertise = "/public/expertise";

    public function getFullPath(): string
    {
        return match ($this) {
            self::Expertise => "public/expertise",
        };
    }
}
