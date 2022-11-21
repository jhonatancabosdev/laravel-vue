<?php

namespace App\Gateways;

use GuzzleHttp\Client;

class NofraudGateway
{
    public $client;
    protected $base_url;
    protected $api_key;

    public function __construct()
    {
        $this->client = new Client();
        $this->base_url = config('services.nofraud.url');
        $this->api_key = config('services.nofraud.api_key');
    }

    public function post($endpoint, $payload)
    {
        $payload = (array) $payload;

        $payload = array_merge($payload, [
            'nfToken' => $this->api_key,
        ]);

        return json_decode($this->client->post(
            $this->base_url . $endpoint,
            [
                'headers' => [
                    'Content-Type'  => 'application/json',
                    'Accept'        => 'application/json',
                ],
                'body'    => json_encode($payload),
            ]
        )->getBody()->getContents());
    }
}
