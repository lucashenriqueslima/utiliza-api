<?php

namespace App\Services\Firebase;

use App\Dtos\FirebasePushNotificationDTO;
use App\Models\Biker;
use App\Models\Call;
use Google_Client;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class FirebaseService
{
    private PendingRequest $client;
    private Google_Client $googleClient;
    private string $googleApiUrl;
    private array $googleApiTokens;

    public function __construct(
        private Call $call,
    ) {
        $this->googleClient = new Google_Client();
        $this->authWithGoogleApi();
        $this->client = Http::withHeaders($this->getHeaders());
    }

    public function sendPushNotification(string $callRequestId, string $bikerFirebaseToken, string $distance, string $createdAt): void
    {
        try {
            $response = $this->client->post($this->googleApiUrl, [
                'message' => [
                    'token' => $bikerFirebaseToken,
                    'notification' => [
                        'title' => 'NOVO CHAMADO!',
                        'body' => 'Você recebeu um novo chamado, clique para visualizar.',
                    ],
                    'data' => [
                        'call_id' => (string)$this->call->id,
                        'call_request_id' => (string)$callRequestId,
                        'address' => $this->call->address,
                        'distance' => str_replace('.', ',', $distance),
                        'time' => (string) number_format($distance * 4),
                        'price' => '50,00',
                        'timeout_response' => (string) $createdAt,
                    ],
                    'android' => [
                        'notification' => [
                            'sound' => 'notification.mp3',
                            'channel_id' => 'com.example.locavibe_renter_app-5',
                        ],
                    ],
                ],
            ]);
        } catch (\Exception $e) {
            dd($e);
        }
    }

    private function authWithGoogleApi(): void
    {
        try {
            $credentialsFilePath = "firebase/fcm.json";
            $this->googleClient->setAuthConfig($credentialsFilePath);
            $this->googleClient->addScope("https://www.googleapis.com/auth/firebase.messaging");

            $this->googleApiUrl = "https://fcm.googleapis.com/v1/projects/" . env('FIREBASE_APP_ID') . "/messages:send";

            $this->googleClient->fetchAccessTokenWithAssertion();

            $this->googleApiTokens = $this->googleClient->getAccessToken();
        } catch (\Exception $e) {
            dd($e);
        }
    }

    private function getHeaders(): array
    {
        return [
            'Authorization' => 'Bearer ' . $this->googleApiTokens['access_token'],
            'Content-Type' => 'application/json; UTF-8',
        ];
    }
}
