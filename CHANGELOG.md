# Changelog

## [Unreleased]
- Supports Guzzle 7: `guzzlehttp/guzzle` constraint is now `^6.5.8 || ^7.0` (Composer 2.10+ blocks all Guzzle 6 releases due to security advisories)
- Allows `psr/http-message` 2.x
- Uses `GuzzleHttp\Utils::defaultUserAgent()` when available instead of the deprecated `GuzzleHttp\default_user_agent()` function
- Removes `Reflection*::setAccessible()` calls from tests (deprecated in PHP 8.5)
- Fix: `GuzzleClient::post()` now wraps errors in `HttpClientException` with the original message, code and previous exception
- Tested on PHP 8.2, 8.3, 8.4 and 8.5

## [2.0.4] - 2026-02-13
- Minimum required PHP version: 7.4 (runtime), 8.2 (development and testing)
- PHPUnit dependency updated in order to solve Unsafe Deserialization vulnerability
- Suppresses return type deprecation warnings when running on PHP 8.2+ while maintaining backward compatibility with PHP 7.4.
- Migrated test suite to PHPUnit 11

## [2.0.3] - 2022-12-05

- Fix: This Now wurfl microservice client sends only its internal user-agent as header in HTTP requests. All headers used for device detection are only sent as JSON request payload.

## [2.0.2] - 2020-07-06
- Update web example to use Laminas\Diactoros. Now requires PHP >= 7.1
- [README] Update installation step with composer

## [2.0.1] - 2020-04-04
- Updated unit tests to run on different WURFL Microservice server configurations

## [2.0.0] - 2020-02-17
- Initial release: All lookup* functions
- getInfo
- All enumerator functions
