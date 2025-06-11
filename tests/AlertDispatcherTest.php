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

    public function testObjectCanBeInstantiated()
    {
        $this->assertInstanceOf(AlertDispatcher::class, new AlertDispatcher(new Exception('Test Exception'),));
    }

    public static function dispatchMethodsShouldProvider()
    {
        yield [
            'method' => 'shouldMail',
            'config' => [
                'laravel_alert_notifications.mail.enabled'   => true,
                'laravel_alert_notifications.mail.toAddress' => 'example@example.com',
            ],
            'shouldDispatch' => true,
        ];

        yield [
            'method' => 'shouldMail',
            'config' => [
                'laravel_alert_notifications.mail.enabled'   => false,
                'laravel_alert_notifications.mail.toAddress' => 'example@example.com',
            ],
            'shouldDispatch' => false,
        ];

        yield [
            'method' => 'shouldMail',
            'config' => [
                'laravel_alert_notifications.mail.enabled'   => true,
                'laravel_alert_notifications.mail.toAddress' => null,
            ],
            'shouldDispatch' => false,
        ];

        yield [
            'method' => 'shouldMail',
            'config' => [
                'laravel_alert_notifications.mail.enabled'   => false,
                'laravel_alert_notifications.mail.toAddress' => null,
            ],
            'shouldDispatch' => false,
        ];

        yield [
            'method' => 'shouldMicrosoftTeams',
            'config' => [
                'laravel_alert_notifications.microsoft_teams.enabled' => true,
                'laravel_alert_notifications.microsoft_teams.webhook' => 'http://example.com',
            ],
            'shouldDispatch' => true,
        ];

        yield [
            'method' => 'shouldMicrosoftTeams',
            'config' => [
                'laravel_alert_notifications.microsoft_teams.enabled' => false,
                'laravel_alert_notifications.microsoft_teams.webhook' => 'http://example.com',
            ],
            'shouldDispatch' => false,
        ];

        yield [
            'method' => 'shouldMicrosoftTeams',
            'config' => [
                'laravel_alert_notifications.microsoft_teams.enabled' => true,
                'laravel_alert_notifications.microsoft_teams.webhook' => null,
            ],
            'shouldDispatch' => false,
        ];

        yield [
            'method' => 'shouldMicrosoftTeams',
            'config' => [
                'laravel_alert_notifications.microsoft_teams.enabled' => false,
                'laravel_alert_notifications.microsoft_teams.webhook' => null,
            ],
            'shouldDispatch' => false,
        ];

        yield [
            'method' => 'shouldSlack',
            'config' => [
                'laravel_alert_notifications.slack.enabled' => true,
                'laravel_alert_notifications.slack.webhook' => 'http://example.com',
            ],
            'shouldDispatch' => true,
        ];

        yield [
            'method' => 'shouldSlack',
            'config' => [
                'laravel_alert_notifications.slack.enabled' => false,
                'laravel_alert_notifications.slack.webhook' => 'http://example.com',
            ],
            'shouldDispatch' => false,
        ];

        yield [
            'method' => 'shouldSlack',
            'config' => [
                'laravel_alert_notifications.slack.enabled' => true,
                'laravel_alert_notifications.slack.webhook' => null,
            ],
            'shouldDispatch' => false,
        ];

        yield [
            'method' => 'shouldSlack',
            'config' => [
                'laravel_alert_notifications.slack.enabled' => false,
                'laravel_alert_notifications.slack.webhook' => null,
            ],
            'shouldDispatch' => false,
        ];

        yield [
            'method' => 'shouldPagerDuty',
            'config' => [
                'laravel_alert_notifications.pager_duty.enabled'         => true,
                'laravel_alert_notifications.pager_duty.integration_key' => 'test',
            ],
            'shouldDispatch' => true,
        ];

        yield [
            'method' => 'shouldPagerDuty',
            'config' => [
                'laravel_alert_notifications.pager_duty.enabled'         => false,
                'laravel_alert_notifications.pager_duty.integration_key' => 'test',
            ],
            'shouldDispatch' => false,
        ];

        yield [
            'method' => 'shouldPagerDuty',
            'config' => [
                'laravel_alert_notifications.pager_duty.enabled'         => true,
                'laravel_alert_notifications.pager_duty.integration_key' => null,
            ],
            'shouldDispatch' => false,
        ];

        yield [
            'method' => 'shouldPagerDuty',
            'config' => [
                'laravel_alert_notifications.pager_duty.enabled'         => false,
                'laravel_alert_notifications.pager_duty.integration_key' => null,
            ],
            'shouldDispatch' => false,
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('dispatchMethodsShouldProvider')]
    public function testDispatchMethodsShouldSend(string $method, array $config, bool $shouldDispatch)
    {
        Mail::fake();
        config($config);

        $alertDispatcher = new AlertDispatcher(new Exception('Test Exception'));

        $this->assertEquals($shouldDispatch, $alertDispatcher->{$method}());
    }

    public function testExceptionCanBeSilenced()
    {
        $alertDispatcher = new AlertDispatcher(new Exception('Test Exception'), []);
        $this->assertFalse($alertDispatcher->isDoNotAlertException());

        $alertDispatcher->dontAlertExceptions = [Exception::class];
        $this->assertTrue($alertDispatcher->isDoNotAlertException());
    }

    public function testShouldAlert()
    {
        Mail::fake();
        config($this->config);

        $alertDispatcher = new AlertDispatcher(new Exception('Test Exception'));

        $this->assertTrue($alertDispatcher->shouldAlert());
    }
}
