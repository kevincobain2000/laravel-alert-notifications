<?php

namespace Kevincobain2000\LaravelEmailExceptions\Tests;

use Kevincobain2000\LaravelAlertNotifications\Dispatcher\Webhook;
use PHPUnit\Framework\TestCase;

class WebhookTest extends TestCase
{
    public function testEncodeBodyReplacesInvalidUtf8InsteadOfFailing()
    {
        $body = ['payload' => ['custom_details' => ['exception_message' => "bad \xB1\x31 bytes"]]];

        $json = Webhook::encodeBody($body);

        $this->assertJson($json);
        $this->assertSame(
            "bad \u{FFFD}1 bytes",
            json_decode($json, true)['payload']['custom_details']['exception_message']
        );
    }

    public function testEncodeBodyLeavesValidUtf8Untouched()
    {
        $body = ['message' => '日本語 héllo'];

        $this->assertSame($body, json_decode(Webhook::encodeBody($body), true));
    }
}
