<?php

namespace App\Services;

use App\Models\Biker;
use Illuminate\Support\Facades\DB;

class BikerGeolocationService
{
    public function update(
        string $bikerId,
        float $latitude,
        float $longitude
    ): void {
        DB::statement(
            "UPDATE biker_geolocations
        SET biker_geolocations.location = POINT(?, ?),
        biker_geolocations.updated_at = DATE_SUB(NOW(), INTERVAL 3 HOUR)
        where biker_geolocations.biker_id = ?",
            [
                $latitude,
                $longitude,
                $bikerId
            ]
        );
    }

    public function getDistanceByBikerId(
        float $latitude,
        float $longitude,
        string $bikerId
    ): float {
        $distance = DB::selectOne(
            'SELECT ST_Distance_Sphere(POINT(?, ?), biker_geolocations.location) AS distance
            FROM biker_geolocations
            WHERE biker_geolocations.biker_id = ?',
            [
                $longitude,
                $latitude,
                $bikerId
            ]
        );

        return $distance->distance;
    }
}
