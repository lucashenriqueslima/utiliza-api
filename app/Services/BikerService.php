<?php

namespace App\Services;

use App\Models\Biker;
use Illuminate\Database\Eloquent\Collection;

class BikerService
{
    public static function getBikersToSendCallRequest(array $bikerIds, float $latitude, float $longitude): array
    {
        return Biker::selectRaw(
            '
        bikers.id,
        firebase_token,
        ST_Distance_Sphere(POINT(?, ?), biker_geolocations.location) AS distance',
            [
                $longitude,
                $latitude
            ]
        )
            ->whereIn('bikers.id', $bikerIds)
            ->get();
    }
}
