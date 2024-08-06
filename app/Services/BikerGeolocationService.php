<?php

namespace App\Services;

use App\Models\Biker;
use Illuminate\Support\Facades\DB;

class BikerGeolocationService
{
    public static function update(string $bikerId, float $latitude, float $longitude): void
    {
        DB::statement(
            "UPDATE biker_geolocations
        SET biker_geolocations.location = POINT(?, ?),
        biker_geolocations.updated_at = NOW()
        where biker_geolocations.biker_id = ?",
            [
                $latitude,
                $longitude,
                $bikerId
            ]
        );
    }
}
