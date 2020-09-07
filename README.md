# ScientiaMobile WURFL Microservice Client for PHP

WURFL Microservice (by ScientiaMobile, Inc.) is a mobile device detection service that can quickly and accurately detect over 500 capabilities of visiting devices. It can differentiate between portable mobile devices, desktop devices, SmartTVs and any other types of devices that have a web browser.

This is the PHP Client API for accessing the WURFL Microservice. The API is released under Open-Source and can be integrated with other open-source or proprietary code. In order to operate, it requires access to a running instance of the WURFL Microservice product, such as:

- WURFL Microservice for Docker: https://www.scientiamobile.com/products/wurfl-microservice-docker-detect-device/

- WURFL Microservice for AWS: https://www.scientiamobile.com/products/wurfl-device-detection-microservice-aws/ 

- WURFL Microservice for Azure: https://www.scientiamobile.com/products/wurfl-microservice-for-azure/

- WURFL Microservice for Google Cloud Platform: https://www.scientiamobile.com/products/wurfl-microservice-for-gcp/

## Requirements

 - `PHP 5.5+` (suggested PHP >= 7.1)
 - `json` extension (almost always included)
 - `curl` extension is recommended

## Installation using composer

```
composer require scientiamobile/wm-client
```

## Examples WURFL Microservice Client

```php
<?php
require_once '/path/to/vendor/autoload.php';

try {
    // First we need to create a WM client instance, to connect to our WM server API at the specified host and port.
    $wmClient = \ScientiaMobile\WMClient\WMClient::create("http", "localhost", "8080");
    // we are activating the caching option in WM client. In order to not use cache, you just to need to omit enableCache call
    $wmClient->enableCache();
} catch (\Exception $e) {
    // problems such as network errors  or internal server problems
    echo $e->getMessage();
    exit;
}

// We ask Wm server API for some Wm server info such as server API version and info about WURFL API and file used by WM server.
$serveInfo = $wmClient->getInfo();
echo "WM server information: " . PHP_EOL;
echo " - WM version: " . $serveInfo->wmVersion() . PHP_EOL;
echo " - WURFL API version: " . $serveInfo->wurflAPIVersion() . PHP_EOL;
echo " - WURFL file info: " . $serveInfo->wurflInfo() . PHP_EOL;
echo PHP_EOL;

// set the capabilities we want to receive from WM server
// Static capabilities
$wmClient->setRequestedStaticCapabilities(["model_name", "brand_name"]);
// Virtual capabilities
$wmClient->setRequestedVirtualCapabilities(["is_smartphone", "form_factor"]);

$ua = "Mozilla/5.0 (iPhone; CPU iPhone OS 5_0 like Mac OS X) AppleWebKit/534.46 (KHTML, like Gecko) Version/5.1 Mobile/9A334 Safari/7534.48.3";

// Perform a device detection calling WM server API
$deviceData = $wmClient->lookupUserAgent($ua);

if ($deviceData->error()) {
    echo "WM client returned an error: " . $deviceData->error() . PHP_EOL;
    exit;
}

// Let's get the device capabilities and print some of them
echo "WURFL device id: " . $deviceData->capabilities("wurfl_id") . PHP_EOL;

// print brand & model (static capabilities)
echo "This device is a: " . $deviceData->capabilities("brand_name") . " " . $deviceData->capabilities("model_name") . PHP_EOL;

// check if device is a smartphone (a virtual capability)
if ($deviceData->capabilities("is_smartphone") === "true") {
    echo "This is a smartphone" . PHP_EOL;
} else {
    echo "This is not a smartphone" . PHP_EOL;
}

// Printing all received capabilities
echo PHP_EOL;
echo "All received capabilities:" . PHP_EOL;

foreach ($deviceData->getAllCapabilities() as $key => $value) {
    echo " - $key: $value" . PHP_EOL;
}

// Get all the device manufacturers, and print the first twenty
$limit = 20;

$deviceMakes = $wmClient->getAllDeviceMakes();

echo PHP_EOL;
echo "Print the first $limit Brand of " . count($deviceMakes) . PHP_EOL;

// Sort the device manufacturer names
sort($deviceMakes);

for ($i = 0; $i < $limit; $i++) {
    echo " - " . $deviceMakes[$i] . PHP_EOL;
}

echo PHP_EOL;

// Now call the WM server to get all device model and marketing names produced by Apple
echo "Print all Model for the Apple Brand" . PHP_EOL;

$modelMktNames = $wmClient->getAllDevicesForMake("Apple");

// Sort $modelMktNames by their model name
array_multisort($modelMktNames);

foreach ($modelMktNames as $modelMktName) {
    echo " - " . $modelMktName->modelName() . " " . $modelMktName->marketingName() . PHP_EOL;
}

// Now call the WM server to get all operative system names
$oses = $wmClient->getAllOSes();

echo PHP_EOL;
echo "Print the list of OSes" . PHP_EOL;

// Sort and print all OS names
sort($oses);

foreach ($oses as $os) {
    echo " - " . $os . PHP_EOL;
}

echo PHP_EOL;
// Let's call the WM server to get all version of the Android OS
echo "Print all versions for the Android OS" . PHP_EOL;

$versions = $wmClient->getAllVersionsForOS("Android");

// Sort all Android version numbers and print them.
sort($versions);

foreach ($versions as $version) {
    echo " - " . $version . PHP_EOL;
}
```

See more in the [examples](examples) folder 

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
