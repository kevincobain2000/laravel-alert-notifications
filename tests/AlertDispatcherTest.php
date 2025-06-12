<?php

namespace Kevincobain2000\LaravelEmailExceptions\Tests;

use Exception;
use Kevincobain2000\LaravelAlertNotifications\Dispatcher\AlertDispatcher;
use Kevincobain2000\LaravelAlertNotifications\Exceptions\AlertDispatchFailedException;
use Kevincobain2000\LaravelAlertNotifications\Exceptions\AlertDispatchMethodFailedException;
use Mail;
use Mockery;
use Orchestra\Testbench\TestCase;
use Throwable;

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

    /**
     * @dataProvider dispatchMethodsShouldProvider
     */
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
        $this->assertFalse($alertDispatcher->isDontAlertException());

        $alertDispatcher->dontAlertExceptions = [Exception::class];
        $this->assertTrue($alertDispatcher->isDontAlertException());
    }

    public function testShouldAlert()
    {
        Mail::fake();
        config($this->config);

        $alertDispatcher = new AlertDispatcher(new Exception('Test Exception'));

        $this->assertTrue($alertDispatcher->shouldAlert());
    }

    public static function failedDispatchProvider()
    {
        $mailFailedException      = new Exception('Mail failed');
        $teamsFailedException     = new Exception('Teams failed');
        $slackFailedException     = new Exception('Slack failed');
        $pagerDutyFailedException = new Exception('PagerDuty failed');

        yield 'mail fails' => [
            'expectedDispatchState' => function ($alertDispatcher) use ($mailFailedException) {
                $alertDispatcher->shouldReceive('sendMail')->andThrow($mailFailedException);
                $alertDispatcher->shouldReceive('sendMicrosoftTeams')->andReturn(null);
                $alertDispatcher->shouldReceive('sendSlack')->andReturn(null);
                $alertDispatcher->shouldReceive('sendPagerDuty')->andReturn(null);
            },
            'expectedException' => function () use ($mailFailedException) {
                $exceptions   = [];
                $exceptions[] = AlertDispatchMethodFailedException::methodFailed('mail', $mailFailedException);

                return AlertDispatchFailedException::dispatchFailed($exceptions);
            },
        ];

        yield 'teams fails' => [
            'expectedDispatchState' => function ($alertDispatcher) use ($teamsFailedException) {
                $alertDispatcher->shouldReceive('sendMail')->andReturn(null);
                $alertDispatcher->shouldReceive('sendMicrosoftTeams')->andThrow($teamsFailedException);
                $alertDispatcher->shouldReceive('sendSlack')->andReturn(null);
                $alertDispatcher->shouldReceive('sendPagerDuty')->andReturn(null);
            },
            'expectedException' => function () use ($teamsFailedException) {
                $exceptions   = [];
                $exceptions[] = AlertDispatchMethodFailedException::methodFailed('microsoftTeams', $teamsFailedException);

                return AlertDispatchFailedException::dispatchFailed($exceptions);
            },
        ];

        yield 'slack fails' => [
            'expectedDispatchState' => function ($alertDispatcher) use ($slackFailedException) {
                $alertDispatcher->shouldReceive('sendMail')->andReturn(null);
                $alertDispatcher->shouldReceive('sendMicrosoftTeams')->andReturn(null);
                $alertDispatcher->shouldReceive('sendSlack')->andThrow($slackFailedException);
                $alertDispatcher->shouldReceive('sendPagerDuty')->andReturn(null);
            },
            'expectedException' => function () use ($slackFailedException) {
                $exceptions   = [];
                $exceptions[] = AlertDispatchMethodFailedException::methodFailed('slack', $slackFailedException);

                return AlertDispatchFailedException::dispatchFailed($exceptions);
            },
        ];

        yield 'pager duty fails' => [
            'expectedDispatchState' => function ($alertDispatcher) use ($pagerDutyFailedException) {
                $alertDispatcher->shouldReceive('sendMail')->andReturn(null);
                $alertDispatcher->shouldReceive('sendMicrosoftTeams')->andReturn(null);
                $alertDispatcher->shouldReceive('sendSlack')->andReturn(null);
                $alertDispatcher->shouldReceive('sendPagerDuty')->andThrow($pagerDutyFailedException);
            },
            'expectedException' => function () use ($pagerDutyFailedException) {
                $exceptions   = [];
                $exceptions[] = AlertDispatchMethodFailedException::methodFailed('pagerDuty', $pagerDutyFailedException);

                return AlertDispatchFailedException::dispatchFailed($exceptions);
            },
        ];

        yield 'mail and teams fail' => [
            'expectedDispatchState' => function ($alertDispatcher) use ($mailFailedException, $teamsFailedException) {
                $alertDispatcher->shouldReceive('sendMail')->andThrow($mailFailedException);
                $alertDispatcher->shouldReceive('sendMicrosoftTeams')->andThrow($teamsFailedException);
                $alertDispatcher->shouldReceive('sendSlack')->andReturn(null);
                $alertDispatcher->shouldReceive('sendPagerDuty')->andReturn(null);
            },
            'expectedException' => function () use ($mailFailedException, $teamsFailedException) {
                $exceptions   = [];
                $exceptions[] = AlertDispatchMethodFailedException::methodFailed('mail', $mailFailedException);
                $exceptions[] = AlertDispatchMethodFailedException::methodFailed('microsoftTeams', $teamsFailedException);

                return AlertDispatchFailedException::dispatchFailed($exceptions);
            },
        ];

        yield 'mail and slack fail' => [
            'expectedDispatchState' => function ($alertDispatcher) use ($mailFailedException, $slackFailedException) {
                $alertDispatcher->shouldReceive('sendMail')->andThrow($mailFailedException);
                $alertDispatcher->shouldReceive('sendMicrosoftTeams')->andReturn(null);
                $alertDispatcher->shouldReceive('sendSlack')->andThrow($slackFailedException);
                $alertDispatcher->shouldReceive('sendPagerDuty')->andReturn(null);
            },
            'expectedException' => function () use ($mailFailedException, $slackFailedException) {
                $exceptions   = [];
                $exceptions[] = AlertDispatchMethodFailedException::methodFailed('mail', $mailFailedException);
                $exceptions[] = AlertDispatchMethodFailedException::methodFailed('slack', $slackFailedException);

                return AlertDispatchFailedException::dispatchFailed($exceptions);
            },
        ];

        yield 'mail and pager duty fail' => [
            'expectedDispatchState' => function ($alertDispatcher) use ($mailFailedException, $pagerDutyFailedException) {
                $alertDispatcher->shouldReceive('sendMail')->andThrow($mailFailedException);
                $alertDispatcher->shouldReceive('sendMicrosoftTeams')->andReturn(null);
                $alertDispatcher->shouldReceive('sendSlack')->andReturn(null);
                $alertDispatcher->shouldReceive('sendPagerDuty')->andThrow($pagerDutyFailedException);
            },
            'expectedException' => function () use ($mailFailedException, $pagerDutyFailedException) {
                $exceptions   = [];
                $exceptions[] = AlertDispatchMethodFailedException::methodFailed('mail', $mailFailedException);
                $exceptions[] = AlertDispatchMethodFailedException::methodFailed('pagerDuty', $pagerDutyFailedException);
                
                return AlertDispatchFailedException::dispatchFailed($exceptions);
            },
        ];

        yield 'teams and slack fail' => [
            'expectedDispatchState' => function ($alertDispatcher) use ($teamsFailedException, $slackFailedException) {
                $alertDispatcher->shouldReceive('sendMail')->andReturn(null);
                $alertDispatcher->shouldReceive('sendMicrosoftTeams')->andThrow($teamsFailedException);
                $alertDispatcher->shouldReceive('sendSlack')->andThrow($slackFailedException);
                $alertDispatcher->shouldReceive('sendPagerDuty')->andReturn(null);
            },
            'expectedException' => function () use ($teamsFailedException, $slackFailedException) {
                $exceptions   = [];
                $exceptions[] = AlertDispatchMethodFailedException::methodFailed('microsoftTeams', $teamsFailedException);
                $exceptions[] = AlertDispatchMethodFailedException::methodFailed('slack', $slackFailedException);

                return AlertDispatchFailedException::dispatchFailed($exceptions);
            },
        ];

        yield 'teams and pager duty fail' => [
            'expectedDispatchState' => function ($alertDispatcher) use ($teamsFailedException, $pagerDutyFailedException) {
                $alertDispatcher->shouldReceive('sendMail')->andReturn(null);
                $alertDispatcher->shouldReceive('sendMicrosoftTeams')->andThrow($teamsFailedException);
                $alertDispatcher->shouldReceive('sendSlack')->andReturn(null);
                $alertDispatcher->shouldReceive('sendPagerDuty')->andThrow($pagerDutyFailedException);
            },
            'expectedException' => function () use ($teamsFailedException, $pagerDutyFailedException) {
                $exceptions   = [];
                $exceptions[] = AlertDispatchMethodFailedException::methodFailed('microsoftTeams', $teamsFailedException);
                $exceptions[] = AlertDispatchMethodFailedException::methodFailed('pagerDuty', $pagerDutyFailedException);

                return AlertDispatchFailedException::dispatchFailed($exceptions);
            },
        ];

        yield 'slack and pager duty fail' => [
            'expectedDispatchState' => function ($alertDispatcher) use ($slackFailedException, $pagerDutyFailedException) {
                $alertDispatcher->shouldReceive('sendMail')->andReturn(null);
                $alertDispatcher->shouldReceive('sendMicrosoftTeams')->andReturn(null);
                $alertDispatcher->shouldReceive('sendSlack')->andThrow($slackFailedException);
                $alertDispatcher->shouldReceive('sendPagerDuty')->andThrow($pagerDutyFailedException);
            },
            'expectedException' => function () use ($slackFailedException, $pagerDutyFailedException) {
                $exceptions   = [];
                $exceptions[] = AlertDispatchMethodFailedException::methodFailed('slack', $slackFailedException);
                $exceptions[] = AlertDispatchMethodFailedException::methodFailed('pagerDuty', $pagerDutyFailedException);

                return AlertDispatchFailedException::dispatchFailed($exceptions);
            },
        ];

        yield 'mail and teams and slack fails' => [
            'expectedDispatchState' => function ($alertDispatcher) use ($mailFailedException, $teamsFailedException, $slackFailedException) {
                $alertDispatcher->shouldReceive('sendMail')->andThrow($mailFailedException);
                $alertDispatcher->shouldReceive('sendMicrosoftTeams')->andThrow($teamsFailedException);
                $alertDispatcher->shouldReceive('sendSlack')->andThrow($slackFailedException);
                $alertDispatcher->shouldReceive('sendPagerDuty')->andReturn(null);
            },
            'expectedException' => function () use ($mailFailedException, $teamsFailedException, $slackFailedException) {
                $exceptions   = [];
                $exceptions[] = AlertDispatchMethodFailedException::methodFailed('mail', $mailFailedException);
                $exceptions[] = AlertDispatchMethodFailedException::methodFailed('microsoftTeams', $teamsFailedException);
                $exceptions[] = AlertDispatchMethodFailedException::methodFailed('slack', $slackFailedException);

                return AlertDispatchFailedException::dispatchFailed($exceptions);
            },
        ];

        yield 'mail and teams and pager duty fails' =>[
            'expectedDispatchState' => function ($alertDispatcher) use ($mailFailedException, $teamsFailedException, $pagerDutyFailedException) {
                $alertDispatcher->shouldReceive('sendMail')->andThrow($mailFailedException);
                $alertDispatcher->shouldReceive('sendMicrosoftTeams')->andThrow($teamsFailedException);
                $alertDispatcher->shouldReceive('sendSlack')->andReturn(null);
                $alertDispatcher->shouldReceive('sendPagerDuty')->andThrow($pagerDutyFailedException);
            },
            'expectedException' => function () use ($mailFailedException, $teamsFailedException, $pagerDutyFailedException) {
                $exceptions   = [];
                $exceptions[] = AlertDispatchMethodFailedException::methodFailed('mail', $mailFailedException);
                $exceptions[] = AlertDispatchMethodFailedException::methodFailed('microsoftTeams', $teamsFailedException);
                $exceptions[] = AlertDispatchMethodFailedException::methodFailed('pagerDuty', $pagerDutyFailedException);

                return AlertDispatchFailedException::dispatchFailed($exceptions);
            },
        ];

        yield 'mail and slack and pager duty fails' => [
            'expectedDispatchState' => function ($alertDispatcher) use ($mailFailedException, $slackFailedException, $pagerDutyFailedException) {
                $alertDispatcher->shouldReceive('sendMail')->andThrow($mailFailedException);
                $alertDispatcher->shouldReceive('sendMicrosoftTeams')->andReturn(null);
                $alertDispatcher->shouldReceive('sendSlack')->andThrow($slackFailedException);
                $alertDispatcher->shouldReceive('sendPagerDuty')->andThrow($pagerDutyFailedException);
            },
            'expectedException' => function () use ($mailFailedException, $slackFailedException, $pagerDutyFailedException) {
                $exceptions   = [];
                $exceptions[] = AlertDispatchMethodFailedException::methodFailed('mail', $mailFailedException);
                $exceptions[] = AlertDispatchMethodFailedException::methodFailed('slack', $slackFailedException);
                $exceptions[] = AlertDispatchMethodFailedException::methodFailed('pagerDuty', $pagerDutyFailedException);

                return AlertDispatchFailedException::dispatchFailed($exceptions);
            },
        ];

        yield 'teams and slack and pager duty fails' => [
            'expectedDispatchState' => function ($alertDispatcher) use ($teamsFailedException, $slackFailedException, $pagerDutyFailedException) {
                $alertDispatcher->shouldReceive('sendMail')->andReturn(null);
                $alertDispatcher->shouldReceive('sendMicrosoftTeams')->andThrow($teamsFailedException);
                $alertDispatcher->shouldReceive('sendSlack')->andThrow($slackFailedException);
                $alertDispatcher->shouldReceive('sendPagerDuty')->andThrow($pagerDutyFailedException);
            },
            'expectedException' => function () use ($teamsFailedException, $slackFailedException, $pagerDutyFailedException) {
                $exceptions   = [];
                $exceptions[] = AlertDispatchMethodFailedException::methodFailed('microsoftTeams', $teamsFailedException);
                $exceptions[] = AlertDispatchMethodFailedException::methodFailed('slack', $slackFailedException);
                $exceptions[] = AlertDispatchMethodFailedException::methodFailed('pagerDuty', $pagerDutyFailedException);

                return AlertDispatchFailedException::dispatchFailed($exceptions);
            },
        ];
    }

    /**
     * @dataProvider failedDispatchProvider
     */
    public function testFailureInDispatchMethodDoesNotImpactOtherDispatchMethods(callable $expectedDispatchState, callable $expectedException)
    {
        Mail::fake();
        config($this->config);

        $alertDispatcher = Mockery::mock(
            AlertDispatcher::class,
            [new Exception('Test Exception')]
        )->makePartial()->shouldAllowMockingProtectedMethods();

        $expectedDispatchState($alertDispatcher);

        try {
            $alertDispatcher->notify();
        } catch (Throwable $e) {
            $this->assertEquals($expectedException(), $e);
        }
    }
}
