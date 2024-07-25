<?php

namespace App\Services\Firebase;

use Google_Client;
use Illuminate\Support\Facades\Log;

class FirebaseAuthService
{
    private Google_Client $googleClient;
    private string $credentialsFilePath;
    private array $authencationData;
    public function __construct()
    {
        $this->credentialsFilePath = base_path() . "/firebase/fcm.json";
        $this->googleClient = new Google_Client();
        $this->authencationData = $this->authenticate();
    }

    public function getAccessToken(): string
    {
        return $this->authencationData['access_token'];
    }

    private function authenticate(): array
    {
        try {
            $this->googleClient->setAuthConfig($this->credentialsFilePath);
            $this->googleClient->addScope("https://www.googleapis.com/auth/firebase.messaging");
            $this->googleClient->fetchAccessTokenWithAssertion();

            return $this->googleClient->getAccessToken();
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            throw $e;
        }
    }
}
