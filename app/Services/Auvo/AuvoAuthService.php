<?php

namespace App\Services\Auvo;

use App\Enums\AuvoDepartment;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class AuvoAuthService
{
    private PendingRequest $client;
    private array $authencationData;
    public function __construct(
        private AuvoDepartment $auvoDepartment
    ) {
        try {
            $this->client = Http::baseUrl(config('auvo.api_url'))
                ->withHeaders([
                    "Content-Type" => "application/json",
                ]);

            $this->authencationData = $this->authenticate();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function getAccessToken(): string
    {
        return $this->authencationData['accessToken'] ?? throw new \Exception('Falha ao autenticar com a API da Auvo');
    }

    private function authenticate(): array
    {
        try {
            $response = $this->client->get('login', [
                'apiKey' => $this->auvoDepartment->getApiKey(),
                'apiToken' => $this->auvoDepartment->getApiToken(),
            ]);

            if (!$response->ok()) {
                throw new \Exception('Falha ao autenticar com a API da Auvo');
            }

            return $response->json()['result'];
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
