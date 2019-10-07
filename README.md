# ScientiaMobile WURFL Microservice Client for PHP

The WURFL Microservice Service by ScientiaMobile, Inc., is a 
mobile device detection service that can quickly and accurately
detect over 500 capabilities of visiting devices.  It can differentiate
between portable mobile devices, desktop devices, SmartTVs and any 
other types of devices that have a web browser.

This is the PHP Client for accessing the WURFL Microservice Service.

## Requirements

 - `PHP 5.5+`
 - `json` extension (almost always included)
 - `curl` extension is recommended

## Install composer dependencies

    composer install --no-dev --optimize-autoloader

## Examples WURFL Microservice Client

See the [examples](examples) folder 

## Testing
Unit tests are included with the client and can be run with PHPUnit.

    php vendor/bin/phpunit

> Note that in order to get all test to pass, you will need to have an 
instance of Wurfl Microservice Server running on `localhost` port `8080` 
otherwise the integration tests will be skipped.

**2017 ScientiaMobile Incorporated**

**All Rights Reserved.**

**NOTICE**:  All information contained herein is, and remains the property of
ScientiaMobile Incorporated and its suppliers, if any.  The intellectual
and technical concepts contained herein are proprietary to ScientiaMobile
Incorporated and its suppliers and may be covered by U.S. and Foreign
Patents, patents in process, and are protected by trade secret or copyright
law. Dissemination of this information or reproduction of this material is
strictly forbidden unless prior written permission is obtained from 
ScientiaMobile Incorporated.
