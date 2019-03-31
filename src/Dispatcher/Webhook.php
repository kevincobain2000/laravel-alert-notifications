<?php
namespace Kevincobain2000\LaravelAlertNotifications\Dispatcher;

use GuzzleHttp\Client;

class Webhook
{
    public static function send(string $url, array $body, $proxy, string $method = 'POST')
    {
        $client = new Client();
        $result = $client->request($method, $url, [
            'proxy' => $proxy,
            'body' => json_encode($body)
        ]);
        return $result;
    }
}
