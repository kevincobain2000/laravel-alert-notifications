<?php

namespace Kevincobain2000\LaravelEmailExceptions\Tests;

use BadMethodCallException;
use Exception;
use Kevincobain2000\LaravelAlertNotifications\Dispatcher\ThrottleControl;
use Orchestra\Testbench\TestCase;

class ThrottleControlTest extends TestCase
{
    protected $alertHandlerMock;

    protected $config = [
        'laravel_alert_notifications.throttle_enabled'          => true,
        'laravel_alert_notifications.throttle_duration_minutes' => 1,
        'laravel_alert_notifications.cache_prefix'              => 'laravel-alert-notifications-test-',
    ];

    public function setUp()
    {
        parent::setUp();
    }

    public function testIsThrottled()
    {
        config($this->config);
        $exception = new Exception('message');
        $actual    = ThrottleControl::isThrottled($exception);
        $this->assertFalse($actual);

        $actual = ThrottleControl::isThrottled($exception);
        $this->assertTrue($actual);

        $differentException = new BadMethodCallException('message');
        $actual             = ThrottleControl::isThrottled($differentException);
        $this->assertFalse($actual);

        // crud: do it again
        $exception = new Exception('message');
        $actual    = ThrottleControl::isThrottled($exception);
        $this->assertTrue($actual);
    }

    public function testGetThrottleCacheKey()
    {
        config($this->config);
        $exception = new Exception('message', $code = 123);
        $actual    = ThrottleControl::getThrottleCacheKey($exception);
        $this->assertSame($actual, 'laravel-alert-notifications-test-Exception-123');
    }
}
