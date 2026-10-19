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
            'body'            => self::encodeBody($body),
        ]);

        return $result;
    }

    /**
     * Exception messages, URLs and context can contain invalid UTF-8, which makes a plain
     * json_encode() return false and the receiving service reject the request.
     */
    public static function encodeBody(array $body): string
    {
        return json_encode($body, JSON_INVALID_UTF8_SUBSTITUTE | JSON_THROW_ON_ERROR);
    }
}
