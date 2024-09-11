<?php

namespace App\Services\Auvo;

use App\Helpers\FormatHelper;
use App\Jobs\UpdateAuvoCustomerJob;
use App\Models\Ileva\IlevaAccidentInvolved;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Laravel\Octane\Facades\Octane;

class AuvoService
{
    private PendingRequest $authenticatedClient;

    public function __construct(
        private string $accessToken
    ) {
        $this->authenticatedClient = Http::baseUrl(config('auvo.api_url'))
            ->withHeaders($this->getHeaders());
    }

    private function getHeaders(): array
    {
        return [
            'Authorization' => 'Bearer ' . $this->accessToken,
            'Content-Type' => 'application/json',
        ];
    }

    public function getUsers(): array
    {
        try {
            $response = $this->authenticatedClient
                ->get('users', [
                    'page' => 1,
                    'pageSize' => 100,
                    'order' => 'asc',
                ]);

            return $response->json()['result']['entityList'];
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
