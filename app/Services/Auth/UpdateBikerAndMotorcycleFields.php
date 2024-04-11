<?php

namespace App\Services\Auth;

use App\Models\Locavibe\LocavibeRenter;
use App\Models\Biker;

class UpdateBikerAndMotorcycleFields
{
    public static function run(LocavibeRenter $renter): Biker
    {   

        $biker = Biker::updateOrCreate(
            ['locavibe_biker_id' => $renter->id],
            self::fillFieldsBiker($renter)
        );

        $biker->motorcycle()->updateOrCreate(
            ['locavibe_motorcycle_id' => $renter->activationData['vehicle']['id']],
            self::fillFieldsMotorcycle($renter)
        );

        $biker->geolocation()->delete();
        $biker->geolocation()->create();

        return $biker;
    }

    private static function fillFieldsBiker($renter): array
    {
       return [
            'locavibe_biker_id' => $renter->id,
            'name' => $renter->name,
            'email' => $renter->email,
            'phone' => $renter->whatsapp,
            'cpf' => $renter->cpf,
            'cnh' => $renter->cnh,
        ];
    }

    private static function fillFieldsMotorcycle($renter): array
    {
        return [
            'locavibe_motorcycle_id' => $renter->activationData['vehicle']['id'],
            'brand' => $renter->activationData['vehicle']['brand'],
            'model' => $renter->activationData['vehicle']['model'],
            'fipe_year' => $renter->activationData['vehicle']['yearModel'],
            'plate' => $renter->activationData['vehicle']['licensePlate'],
            'color' => $renter->activationData['vehicle']['color'],
            'chassi' => $renter->activationData['vehicle']['chassi'],
            'renavam' => $renter->activationData['vehicle']['renavam'],
            'fipe_code' => $renter->activationData['vehicle']['fipeCode'],
            'motor_number' => $renter->activationData['vehicle']['motorNumber'],
        ];
    }
}