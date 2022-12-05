# Changelog

## [2.0.3] - 2022-12-05

Avoid sending headers from the device that has to be looked up as client request http header. 
They are just sent as request json payload. This avoid issues with Brotly or other framwork/libraries

## [2.0.2] - 2020-07-06
- Update web example to use Laminas\Diactoros. Now requires PHP >= 7.1
- [README] Update installation step with composer

## [2.0.1] - 2020-04-04
- Updated unit tests to run on different WURFL Microservice server configurations

## [2.0.0] - 2020-02-17
- Initial release: All lookup* functions
- getInfo
- All enumerator functions
