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
use Illuminate\Support\Facades\Log as FacadesLog;

class FirebaseService
{
    private PendingRequest $client;
    public function __construct(
        private string $acessToken,
    ) {
        $this->client = Http::withHeaders($this->getHeaders());
    }

    public function sendCallRequestPushNotification(Call $call, CallRequest $callRequest, Biker $biker): void
    {
        try {
            $response = $this->client->post("https://fcm.googleapis.com/v1/projects/" . env('FIREBASE_APP_ID') . "/messages:send", [
                'message' => [
                    'token' => $biker->firebase_token,
                    'notification' => [
                        'title' => 'NOVO CHAMADO!',
                        'body' => 'Você recebeu um novo chamado, clique para visualizar.',
                    ],
                    'data' => [
                        'call_id' => (string)$call->id,
                        'call_request_id' => (string)$callRequest->id,
                        'address' => $call->address,
                        'distance' => str_replace('.', ',', number_format($biker->distance / 1000, 1)),
                        'time' => (string) number_format($biker->distance * 4),
                        'price' => '50,00',
                        'timeout_response' => (string) Carbon::createFromFormat('Y-m-d H:i:s', $callRequest->created_at)->addSeconds(10),
                    ],
                    'android' => [
                        'notification' => [
                            'sound' => 'notification.mp3',
                            'channel_id' => 'com.example.locavibe_renter_app',
                        ],
                    ],
                ],
            ]);

            if ($response->status() !== 200) {
                throw new \Exception('Erro ao enviar notificação para o biker.');
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
