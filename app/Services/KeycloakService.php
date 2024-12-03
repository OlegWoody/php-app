<?php

namespace App\Services;

use GuzzleHttp\Client;

class KeycloakService
{
    protected $client;
    protected $url;
    protected $realm;
    protected $clientId;
    protected $clientSecret;

    public function __construct()
    {
        $this->client = new Client();
        $this->url = env('KEYCLOAK_API_URL');
        $this->realm = env('KEYCLOAK_REALM');
        $this->clientId = env('KEYCLOAK_CLIENT_ID');
        $this->clientSecret = env('KEYCLOAK_CLIENT_SECRET');
    }

    // Метод для получения токена доступа
    public function getAccessToken($username, $password)
    {
        $response = $this->client->post($this->url, [
            'form_params' => [
                'grant_type' => 'password',
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'username' => $username,
                'password' => $password,
            ]
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    // Метод для валидации токена
    public function validateToken($token)
    {
        $response = $this->client->post($this->url, [
            'form_params' => [
                'token' => $token,
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
            ]
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }
}
