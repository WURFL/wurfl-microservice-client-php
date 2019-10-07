<?php

/*
   Copyright 2019 ScientiaMobile Inc. http://www.scientiamobile.com

   Licensed under the Apache License, Version 2.0 (the "License");
   you may not use this file except in compliance with the License.
   You may obtain a copy of the License at

   http://www.apache.org/licenses/LICENSE-2.0

   Unless required by applicable law or agreed to in writing, software
   distributed under the License is distributed on an "AS IS" BASIS,
   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   See the License for the specific language governing permissions and
   limitations under the License.
 */

require_once __DIR__ . '/../vendor/autoload.php';

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
