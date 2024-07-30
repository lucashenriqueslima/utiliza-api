<?php

namespace App\Services\Firebase;

use App\Models\Biker;
use App\Models\Call;
use App\Models\CallRequest;
use AWS\CRT\Log;
use Carbon\Carbon;
use Google_Client;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Number;
use Illuminate\Support\Facades\Log as FacadesLog;

class FirebaseService
{
    private PendingRequest $client;
    private readonly string $pushNotificationUri;
    public function __construct(
        private string $acessToken,
    ) {
        $this->pushNotificationUri = "https://fcm.googleapis.com/v1/projects/" . env('FIREBASE_APP_ID') . "/messages:send";
        $this->client = Http::withHeaders($this->getHeaders());
    }

    public function sendCallRequestPushNotification(Call $call, CallRequest $callRequest, Biker $biker, int|float $distanceInKm): void
    {
        try {
            $response = $this->client->post($this->pushNotificationUri, [
                'message' => [
                    'token' => $biker->firebase_token,
                    'notification' => [
                        'title' => 'NOVO CHAMADO!',
                        'body' => 'Você recebeu um novo chamado, clique para visualizar.',
                    ],
                    'android' => [
                        'notification' => [
                            'sound' => 'notification.mp3',
                            'channel_id' => 'com.example.locavibe_renter_app',
                        ],
                    ],
                    'data' => [
                        'path' => '/dashboard',
                        'call_id' => (string)$call->id,
                        'call_request_id' => (string)$callRequest->id,
                        'address' => $call->address,
                        'distance' => Number::format($distanceInKm * 1.5, precision: 1),
                        'time' => (string) Number::format($distanceInKm * 3.5, precision: 0),
                        'price' => '50,00',
                        'timeout_response' => (string) Carbon::createFromFormat('Y-m-d H:i:s', $callRequest->created_at)->addSeconds(25),
                    ],
                ],
            ]);

            if ($response->status() !== 200) {
                FacadesLog::error($response->body());
            }
        } catch (\Exception $e) {
            FacadesLog::error($e->getMessage());
            throw $e;
        }
    }

    public function sendPushNotificationAfterValidation(string $bikerFirebaseToken, string $body): void
    {
        try {
            $response = $this->client->post($this->pushNotificationUri, [
                'message' => [
                    'token' => $bikerFirebaseToken,
                    'notification' => [
                        'title' => 'Perícia Principal Validada com Sucesso!',
                        'body' => 'Validação foi concluída, clique para visualizar.',
                    ],
                    'data' => [
                        'update_expertise_errors' => 'true',
                    ],
                    'android' => [
                        'notification' => [
                            'sound' => 'notification.mp3',
                        ],
                    ],
                ],
            ]);

            if ($response->status() !== 200) {
                throw new \Exception('Erro ao enviar notificação.');
            }
        } catch (\Exception $e) {
            FacadesLog::error($e->getMessage());
            throw $e;
        }
    }

    private function getHeaders(): array
    {
        return [
            'Authorization' => 'Bearer ' . $this->acessToken,
            'Content-Type' => 'application/json; UTF-8',
        ];
    }
}
