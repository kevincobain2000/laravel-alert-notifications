<?php

namespace Kevincobain2000\LaravelAlertNotifications\Tests;

use Exception;
use Illuminate\Support\Facades\Mail;
use Kevincobain2000\LaravelAlertNotifications\Dispatcher\AlertDispatcher;
use Kevincobain2000\LaravelAlertNotifications\Mail\ExceptionOccurredMail;
use Orchestra\Testbench\TestCase;
use Psr\Log\LogLevel;

class NotificationLevelTest extends TestCase
{
    protected $exception;

    protected $config = [
        'laravel_alert_notifications.mail.enabled'                => true,
        'laravel_alert_notifications.mail.toAddress'              => 'test@test.com',
        'laravel_alert_notifications.mail.fromAddress'            => 'test@test.com',
        'laravel_alert_notifications.mail.subject'                => 'test',
        'laravel_alert_notifications.mail.default_error_level'    => 'error',
        'laravel_alert_notifications.mail.warning.toAddress'      => 'warning_test@test.com',
        'laravel_alert_notifications.mail.error.toAddress'        => 'error_test@test.com',
        'laravel_alert_notifications.exclude_notification_levels' => ['debug'],
    ];

    public function setUp()
    {
        parent::setUp();

        $this->exception = new Exception('Test Exception');
        config($this->config);
    }

    public function testWarningLevelMail()
    {
        $notificationLevelsMapping = [
            Exception::class => LogLevel::WARNING,
        ];

        $alertHandler = new AlertDispatcher($this->exception, [], $notificationLevelsMapping);

        $mailable         = new ExceptionOccurredMail($this->exception, $alertHandler->notificationLevel, []);
        $mailableInstance = $mailable->build();

        $this->assertEquals(
            $mailableInstance->to[0]['address'],
            $this->config['laravel_alert_notifications.mail.warning.toAddress']
        );
    }

    public function testDefaultLevelMail()
    {
        $notificationLevelsMapping = [
            Exception::class => LogLevel::EMERGENCY,
        ];

        $alertHandler = new AlertDispatcher($this->exception, [], $notificationLevelsMapping);

        $mailable         = new ExceptionOccurredMail($this->exception, $alertHandler->notificationLevel, []);
        $mailableInstance = $mailable->build();

        $this->assertEquals(
            $mailableInstance->to[0]['address'],
            $this->config['laravel_alert_notifications.mail.toAddress']
        );
    }

    public function testDoNotSendMail()
    {
        $notificationLevelsMapping = [
            Exception::class => LogLevel::DEBUG,
        ];

        $alertHandler = new AlertDispatcher($this->exception, [], $notificationLevelsMapping);

        Mail::fake();

        $alertHandler->notify();

        Mail::assertNothingSent();
    }
}
