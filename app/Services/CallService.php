<?php

namespace App\Services;

use App\Enums\CallStatus;

class CallService
{


    public static function extractCoordinatesFromGoogleMapsUrl(string $url): ?array
    {

        $pattern2 = '/maps\?q=(-?\d+\.\d+),(-?\d+\.\d+)/';
        $pattern1 = '/3d(-?\d+\.\d+)!4d(-?\d+\.\d+)/';

        if (preg_match($pattern1, $url, $matches)) {
            $latitude = $matches[1];
            $longitude = $matches[2];

            // dd($matches);
        } elseif (preg_match($pattern2, $url, $matches)) {
            $latitude = $matches[1];
            $longitude = $matches[2];
        } else {
            return null;
        }

        return [
            'lat' => floatval($latitude),
            'lng' => floatval($longitude)
        ];
    }

    public static function checkIfCallWasAccepted(CallStatus $status)
    {
        if ($status != CallStatus::SearchingBiker) {
            return true;
        }

        return false;
    }
}
