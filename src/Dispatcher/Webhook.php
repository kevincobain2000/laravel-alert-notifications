<?php

namespace Kevincobain2000\LaravelAlertNotifications\Dispatcher;

use GuzzleHttp\Client;

class Webhook
{
    public static function send(string $url, array $body, $proxy, string $method = 'POST')
    {
        $client = new Client();
        $result = $client->request($method, $url, [
            'headers'         => [
                'Content-Type' => 'application/json',
            ],
            'proxy'           => $proxy,
            'connect_timeout' => 5.0,
            'timeout'         => 5.0,
            'body'            => json_encode($body),
        ]);

        return $result;
    }
}
