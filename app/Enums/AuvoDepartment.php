<?php

namespace App\Enums;

enum AuvoDepartment: string
{
    case Expertise = 'expertise';
    case Inspection = 'inspection';
    case Tracking = 'tracking';

    public function getApiKey(): string
    {
        return match ($this) {
            self::Expertise => config('auvo.expertise.api_key'),
            self::Inspection => config('auvo.inspection.api_key'),
            self::Tracking => config('auvo.tracking.api_key'),
            default => 'Unknown',
        };
    }

    public function getApiToken(): string
    {
        return match ($this) {
            self::Expertise => config('auvo.expertise.api_token'),
            self::Inspection => config('auvo.inspection.api_token'),
            self::Tracking =>  config('auvo.tracking.api_token'),
            default => 'Unknown',
        };
    }
}
