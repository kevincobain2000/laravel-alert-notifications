<?php

namespace Kevincobain2000\LaravelEmailExceptions\Tests;

use Exception;
use Kevincobain2000\LaravelAlertNotifications\Dispatcher\AlertDispatcher;
use Mail;
use Mockery;
use Orchestra\Testbench\TestCase;

class AlertDispatcherTest extends TestCase
{
    protected $alertHandlerMock;

    protected $config = [
        'laravel_alert_notifications.throttle_enabled'              => true,
        'laravel_alert_notifications.throttle_duration_minutes'     => 100,
        'laravel_alert_notifications.mail.enabled'                  => true,
        'laravel_alert_notifications.mail.toAddress'                => 'test@test.com',
        'laravel_alert_notifications.mail.fromAddress'              => 'test@test.com',
        'laravel_alert_notifications.mail.subject'                  => 'test',
        'laravel_alert_notifications.microsoft_teams.enabled'       => true,
        'laravel_alert_notifications.microsoft_teams.webhook'       => 'http://test',
        'laravel_alert_notifications.slack.enabled'                 => true,
        'laravel_alert_notifications.slack.webhook'                 => 'http://test',
        'laravel_alert_notifications.pager_duty.enabled'            => true,
        'laravel_alert_notifications.pager_duty.events_v2_endpoint' => 'http://test',
        'laravel_alert_notifications.pager_duty.integration_key'    => 'test',
        'laravel_alert_notifications.cache_prefix'                  => 'laravel-alert-notifications-test-',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->alertHandlerMock = Mockery::mock(
            AlertDispatcher::class
        )->makePartial()->shouldAllowMockingProtectedMethods();
    }

    public function testAlert()
    {
        $exception                                   = new Exception('Test Exception');
        $this->alertHandlerMock->exception           = $exception;
        $this->alertHandlerMock->dontAlertExceptions = [];

        config($this->config);

        Mail::fake();

        $actual = $this->alertHandlerMock->shouldMail();
        $this->assertTrue($actual);

        $actual = $this->alertHandlerMock->shouldMicrosoftTeams();
        $this->assertTrue($actual);

        $actual = $this->alertHandlerMock->shouldSlack();
        $this->assertTrue($actual);

        $actual = $this->alertHandlerMock->shouldPagerDuty();
        $this->assertTrue($actual);

        $this->alertHandlerMock->dontAlertExceptions = [Exception::class];
        $actual                                      = $this->alertHandlerMock->isDonotAlertException();
        $this->assertTrue($actual);
    }

    public function testShouldAlert()
    {
        $exception                                   = new Exception('Test Exception');
        $this->alertHandlerMock->exception           = $exception;
        $this->alertHandlerMock->dontAlertExceptions = [];

        config($this->config);

        Mail::fake();

        $actual = $this->alertHandlerMock->shouldAlert();
        $this->assertTrue($actual);
    }

    public function testBasic()
    {
        $alert = new AlertDispatcher(new Exception(), []);
        $this->assertTrue(true);
    }
}
