# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [6.3] - 2026-10-19

### Fixed

- Webhook alerts no longer fail when the payload contains invalid UTF-8 (for example in exception messages or URLs). JSON encoding now substitutes invalid sequences instead of failing silently and sending a rejected body to services such as PagerDuty.

## [6.2] - 2025-07-31

### Changed

- Slack messages are cleaner and now colored by notification level. Colors are configurable via `slack.notification_color` (and the matching `ALERT_NOTIFICATION_SLACK_NOTIFICATION_COLOR_*` environment variables). ([#52](https://github.com/kevincobain2000/laravel-alert-notifications/pull/52))

## [6.1] - 2025-06-13

### Added

- PagerDuty notifications via the Events v2 API, with `pager_duty.enabled`, `pager_duty.events_v2_endpoint`, `pager_duty.integration_key` and `pager_duty.proxy` configuration. ([#51](https://github.com/kevincobain2000/laravel-alert-notifications/pull/51))
- Laravel 12 support. ([#51](https://github.com/kevincobain2000/laravel-alert-notifications/pull/51))
- `AlertDispatchMethodFailedException` and `AlertDispatchFailedException`. ([#51](https://github.com/kevincobain2000/laravel-alert-notifications/pull/51))

### Changed

- Every notification channel is now attempted even if an earlier one fails. Failures are collected and thrown together as an `AlertDispatchFailedException` once all channels have been tried. ([#51](https://github.com/kevincobain2000/laravel-alert-notifications/pull/51))
- `AlertDispatcher::shouldAlert()` and the `should*()` channel methods are now public. ([#51](https://github.com/kevincobain2000/laravel-alert-notifications/pull/51))
- Renamed `AlertDispatcher::isDonotAlertException()` to `isDontAlertException()` and made it public. ([#51](https://github.com/kevincobain2000/laravel-alert-notifications/pull/51))

## [6.0] - 2024-07-21

### Changed

- Microsoft Teams alerts are now sent as Adaptive Cards for Teams Workflows, because Microsoft is retiring the legacy Office 365 connector webhooks. The Teams webhook URL must be a Workflows webhook. ([#49](https://github.com/kevincobain2000/laravel-alert-notifications/pull/49), fixes [#47](https://github.com/kevincobain2000/laravel-alert-notifications/issues/47))
- Raised the minimum supported versions of Laravel 8 (`^8.79`) and Laravel 9 (`^9.33`). ([#45](https://github.com/kevincobain2000/laravel-alert-notifications/pull/45))
- Reworked the CI workflows to test each PHP and Laravel version combination ([#45](https://github.com/kevincobain2000/laravel-alert-notifications/pull/45)), replaced deprecated `set-output` ([#44](https://github.com/kevincobain2000/laravel-alert-notifications/pull/44)) and updated them for Laravel 11 and PHP 8.3 ([#40](https://github.com/kevincobain2000/laravel-alert-notifications/pull/40), [#41](https://github.com/kevincobain2000/laravel-alert-notifications/pull/41), [#42](https://github.com/kevincobain2000/laravel-alert-notifications/pull/42), [#43](https://github.com/kevincobain2000/laravel-alert-notifications/pull/43)).
- Refreshed the README and replaced the obsolete Travis CI and Scrutinizer badges. ([#46](https://github.com/kevincobain2000/laravel-alert-notifications/pull/46))

## [5.5] - 2024-02-27

### Added

- Laravel 11 support. ([#39](https://github.com/kevincobain2000/laravel-alert-notifications/pull/39), by @laravel-shift)

## [5.4] - 2023-02-16

### Removed

- Dropped support for PHP below 7.3 and for Laravel 5 and 6. ([#38](https://github.com/kevincobain2000/laravel-alert-notifications/pull/38))

## [5.3] - 2023-02-16

### Added

- Laravel 10 support. ([#37](https://github.com/kevincobain2000/laravel-alert-notifications/pull/37), by @kitloong)
- PHP 8.2 in the test matrix.

### Changed

- Migrated `phpunit.xml` to the PHPUnit 10 format.

## [5.2] - 2022-02-15

### Added

- Laravel 9 support. ([#35](https://github.com/kevincobain2000/laravel-alert-notifications/pull/35))

## [5.1] - 2020-12-23

### Added

- Laravel 8 and Guzzle 7 support. ([#32](https://github.com/kevincobain2000/laravel-alert-notifications/pull/32), by @masashi1014)
- GitHub Actions workflow for tests. ([#25](https://github.com/kevincobain2000/laravel-alert-notifications/pull/25))

## [5.0] - 2020-03-10

### Added

- Laravel 7 support. ([#14](https://github.com/kevincobain2000/laravel-alert-notifications/pull/14), by @kitloong)

### Changed

- Type-hint `Throwable` instead of `Exception`.

## [4.4] - 2020-02-21

### Fixed

- Reverted the mail view name change introduced in 4.3.

## [4.3] - 2020-02-17

### Changed

- The mail view is now resolved as `vendor.laravel_alert_notifications.mail` to support Lumen and custom templates. ([#13](https://github.com/kevincobain2000/laravel-alert-notifications/pull/13), by @amitapandey-33)

## [4.2] - 2020-01-21

### Fixed

- Invalid UTF-8 in exception messages and stack traces no longer produces an empty mail body. `htmlspecialchars` now uses `ENT_SUBSTITUTE` to replace invalid sequences. ([#12](https://github.com/kevincobain2000/laravel-alert-notifications/pull/12), by @kitloong)

## [4.1] - 2019-12-12

### Fixed

- Corrected the mail view name. ([#11](https://github.com/kevincobain2000/laravel-alert-notifications/pull/11), by @ikari7789)

## [4.0] - 2019-12-03

### Added

- Notification levels, using the levels defined in `\Psr\Log\LogLevel`. ([#9](https://github.com/kevincobain2000/laravel-alert-notifications/pull/9), by @persimm0n)
- Placeholders in the e-mail subject.
- Context and server name in the templates.

## [3.2] - 2019-10-22

### Added

- Laravel 6 support. ([#7](https://github.com/kevincobain2000/laravel-alert-notifications/pull/7))

## [3.1] - 2019-04-02

### Added

- 5 second connect and request timeouts for webhook requests.

## [3.0] - 2019-03-31

### Changed

- Webhook requests are sent with Guzzle instead of cURL. `guzzlehttp/guzzle` is now a dependency.

## [2.5] - 2019-03-21

### Fixed

- Slack configuration bug.

### Changed

- Moved throttling into a dedicated `ThrottleControl` class.

## [2.4] - 2019-03-21

### Added

- Exception message in the Slack payload. ([#1](https://github.com/kevincobain2000/laravel-alert-notifications/pull/1), by @AlexHupe)
- Request URL in the notification. ([#2](https://github.com/kevincobain2000/laravel-alert-notifications/pull/2), by @AlexHupe)

## [2.3] - 2019-03-11

### Added

- Slack and Microsoft Teams notification channels, so alerts can go to multiple channels.
- Laravel auto-discovery.

### Changed

- Redesigned the package: it no longer extends the exception `Handler`.
- The throttle key now uses the exception class and code instead of the message, because apps often use a custom exception per user.
- Raised the minimum PHP version to 7.0.

### Removed

- The global throttle.

## [2.2] - 2019-03-11

### Changed

- Moved the repository.

## [1.2.1] - 2017-11-25

### Changed

- Documented that `toEmailAddress` accepts an array.
- Reworked the Travis CI configuration.

## [1.2.0] - 2017-07-05

### Added

- Laravel 5.5 package auto-discovery.

## [1.1.3] - 2017-05-29

### Changed

- Read environment values through config instead of the `env()` helper, so config caching works on Laravel 5.2 and later.

## [1.1.2] - 2017-05-19

### Changed

- Refactored to use `shouldntReport`.

## [1.1.1] - 2017-05-18

### Fixed

- The "don't report" list was not respected when sending e-mails.

## [1.1.0] - 2017-05-03

### Added

- Configurable e-mail subject (`emailSubject`), by @mattusik.

## [1.0.0] - 2017-01-21

### Added

- Initial release: e-mail notifications for exceptions with a throttle limit.

### Fixed

- Global throttle limit bug.

[Unreleased]: https://github.com/kevincobain2000/laravel-alert-notifications/compare/6.2...HEAD
[6.2]: https://github.com/kevincobain2000/laravel-alert-notifications/compare/6.1...6.2
[6.1]: https://github.com/kevincobain2000/laravel-alert-notifications/compare/6.0...6.1
[6.0]: https://github.com/kevincobain2000/laravel-alert-notifications/compare/5.5...6.0
[5.5]: https://github.com/kevincobain2000/laravel-alert-notifications/compare/5.4...5.5
[5.4]: https://github.com/kevincobain2000/laravel-alert-notifications/compare/5.3...5.4
[5.3]: https://github.com/kevincobain2000/laravel-alert-notifications/compare/5.2...5.3
[5.2]: https://github.com/kevincobain2000/laravel-alert-notifications/compare/5.1...5.2
[5.1]: https://github.com/kevincobain2000/laravel-alert-notifications/compare/5.0...5.1
[5.0]: https://github.com/kevincobain2000/laravel-alert-notifications/compare/4.4...5.0
[4.4]: https://github.com/kevincobain2000/laravel-alert-notifications/compare/4.3...4.4
[4.3]: https://github.com/kevincobain2000/laravel-alert-notifications/compare/4.2...4.3
[4.2]: https://github.com/kevincobain2000/laravel-alert-notifications/compare/4.1...4.2
[4.1]: https://github.com/kevincobain2000/laravel-alert-notifications/compare/4.0...4.1
[4.0]: https://github.com/kevincobain2000/laravel-alert-notifications/compare/3.2...4.0
[3.2]: https://github.com/kevincobain2000/laravel-alert-notifications/compare/3.1...3.2
[3.1]: https://github.com/kevincobain2000/laravel-alert-notifications/compare/3.0...3.1
[3.0]: https://github.com/kevincobain2000/laravel-alert-notifications/compare/2.5...3.0
[2.5]: https://github.com/kevincobain2000/laravel-alert-notifications/compare/2.4...2.5
[2.4]: https://github.com/kevincobain2000/laravel-alert-notifications/compare/2.3...2.4
[2.3]: https://github.com/kevincobain2000/laravel-alert-notifications/compare/2.2...2.3
[2.2]: https://github.com/kevincobain2000/laravel-alert-notifications/compare/1.2.1...2.2
[1.2.1]: https://github.com/kevincobain2000/laravel-alert-notifications/compare/1.2.0...1.2.1
[1.2.0]: https://github.com/kevincobain2000/laravel-alert-notifications/compare/1.1.3...1.2.0
[1.1.3]: https://github.com/kevincobain2000/laravel-alert-notifications/compare/1.1.2...1.1.3
[1.1.2]: https://github.com/kevincobain2000/laravel-alert-notifications/compare/1.1.1...1.1.2
[1.1.1]: https://github.com/kevincobain2000/laravel-alert-notifications/compare/1.1.0...1.1.1
[1.1.0]: https://github.com/kevincobain2000/laravel-alert-notifications/compare/1.0.0...1.1.0
[1.0.0]: https://github.com/kevincobain2000/laravel-alert-notifications/releases/tag/1.0.0
