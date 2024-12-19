<?php

namespace App\Services\Asaas;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class AupetAsaasService
{
    // private PendingRequest $client;

    public function __construct() {}

    public static function getCustomer(string $cpf): mixed
    {
        try {

            $client = Http::withHeaders([
                'access_token' => "$" . config('asaas.aupet.token') . "==",
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->baseUrl(config('asaas.url'));

            $response = $client->get('customers', [
                'cpfCnpj' => $cpf
            ]);

            if ($response->failed()) {
                throw new \Exception('Falha ao buscar dados do cliente');
            }

            if (empty($response->json()['data'])) {
                return [];
            }
            return [
                'name' => $response->json()['data'][0]['name'],
                'phone_number' => $response->json()['data'][0]['mobilePhone'],
                'email' => $response->json()['data'][0]['email'],
            ];
        } catch (\Exception $e) {
            dd($e);
            throw new \Exception('Falha ao buscar dados do cliente');
        }
    }
}
