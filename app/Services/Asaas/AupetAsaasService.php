<?php

namespace App\Services\Asaas;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class AupetAsaasService
{
    private PendingRequest $client;

    public function __construct()
    {

        $this->client = Http::withHeaders([
            'access_token' => "$" . config('asaas.aupet.token') . "==",
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->baseUrl(config('asaas.url'));
    }

    public function associateHasAupetBenefitActive(string $cpf): bool
    {
        $customer = $this->getCustomer($cpf);

        if (empty($customer['data'])) {
            return false;
        }

        return true;
    }


    private function getCustomer(string $cpf): mixed
    {
        try {
            $response = $this->client->get('customers', [
                'cpfCnpj' => $cpf,
            ]);

            if ($response->failed()) {
                throw new \Exception('Falha ao buscar dados do cliente');
            }
            return $response->json();
        } catch (\Exception $e) {
            throw new \Exception('Falha ao buscar dados do cliente');
        }
    }
}
