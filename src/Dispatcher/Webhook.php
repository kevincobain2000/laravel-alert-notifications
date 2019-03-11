<?php
namespace Kevincobain2000\LaravelAlertNotifications\Dispatcher;

class Webhook
{
    public static function send(string $url, array $body, $proxy, string $method = 'POST')
    {
        $ch = curl_init($url);
        $json = json_encode($body);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($json)
        ]);

        if ($proxy) {
            curl_setopt($ch, CURLOPT_PROXY, $proxy);
        }
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
}
