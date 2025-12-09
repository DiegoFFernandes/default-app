<?php

namespace App\Services;

use Google\Auth\Credentials\ServiceAccountCredentials;
use GuzzleHttp\Client;

class FCMService
{
    private $projectId;
    private $credentials;
    private $client;

    public function __construct()
    {
        $this->projectId = config('services.fcm.project_id');
        $this->credentials = storage_path('app/firebase/service-account.json');

        $this->client = new Client();
    }

    private function getAccessToken()
    {
        $scopes = ['https://www.googleapis.com/auth/firebase.messaging'];
        $credentials = new ServiceAccountCredentials($scopes, $this->credentials);

        return $credentials->fetchAuthToken()['access_token'];
    }

    public function sendToToken($token, $title, $body)
    {
        $url = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";

        $accessToken = $this->getAccessToken();

        $payload = [
            "message" => [
                "token" => $token,
                "data" => [
                    "title" => $title,
                    "body" => $body,
                    "url" => "https://atz.dbytech.com.br/usuario/configuration-users",
                    "icon" => "https://atz.dbytech.com.br/apple-touch-icon.png"
                ]
            ]
        ];

        $response = $this->client->post($url, [
            'headers' => [
                'Authorization' => "Bearer {$accessToken}",
                'Content-Type' => 'application/json',
            ],
            'json' => $payload
        ]);

        return json_decode($response->getBody(), true);
    }
}
