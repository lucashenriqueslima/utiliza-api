<?php

namespace App\Services\Auth;

use App\Models\Biker;
use App\Models\Locavibe\LocavibeRenter;
use Illuminate\Support\Str;

class AuthService
{
    public function generateAuthenticationToken(): string
    {
        return (string) rand(1000, 9999);
    }

    public function saveMailAuthToken(Biker $partiner, string $authToken): void
    {
        $partiner->auth_token = $authToken;
        $partiner->auth_token_verified = false;

        $partiner->save();
    }

    public function maskEmail(string $email): string
    {
        $emailExploded = explode('@', $email);
        $emailExploded[0] = Str::of($emailExploded[0])->mask('*', 3);

        return implode('@', $emailExploded);
    }

    public function updateOrCreatePartinerByLocavibeRenter(?LocavibeRenter $renter): ?Biker
    {

        if (!$renter) {
            return null;
        }

        $biker = Biker::updateOrCreate(
            ['locavibe_biker_id' => $renter->id],
            $this->fillFieldsBiker($renter)
        );

        return $biker;
    }

    public function handlePartinerNewGeolocation(Biker $partiner)
    {
        $partiner->geolocation()->delete();
        $partiner->geolocation()->create();
    }

    private function fillFieldsBiker($renter): array
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

    private function fillFieldsMotorcycle($renter): array
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
