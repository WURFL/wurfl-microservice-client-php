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
require_once __DIR__ . '/vendor/autoload.php';

if (version_compare(PHP_VERSION, "7.1", "<")) {
    echo "This example requires PHP >= 7.1";
    exit;
}

if (!class_exists("\Laminas\Diactoros\ServerRequestFactory")) {
    echo "This example uses the Laminas\Diactoros library. Please install with: composer require laminas/laminas-diactoros";
}

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
echo "WM server information: <br>";
echo "<ul>";
echo "<li>WM version: " . $serveInfo->wmVersion() . "</li>";
echo "<li>WURFL API version: " . $serveInfo->wurflAPIVersion() . "</li>";
echo "<li>WURFL file info: " . $serveInfo->wurflInfo() . "</li>";
echo "</ul>";

// set the capabilities we want to receive from WM server
// Static capabilities
$wmClient->setRequestedStaticCapabilities(["model_name", "brand_name"]);
// Virtual capabilities
$wmClient->setRequestedVirtualCapabilities(["is_smartphone", "form_factor"]);

// Fetch the server request
// Note: In this example we'll using Laminas Diactoros to create a PSR7 request 
// from the supplied superglobal values and requires. 
// Laminas Diactoros requires PHP >= 7.1 
// The library can be installed via composer:
// composer require laminas/laminas-diactoros
$serverRequest = \Laminas\Diactoros\ServerRequestFactory::fromGlobals();

// Perform a device detection calling WM server API
$deviceData = $wmClient->lookupRequest($serverRequest);

if ($deviceData->error()) {
    echo "WM client returned an error: " . $deviceData->error() . "<br>";
    exit;
}

// Let's get the device capabilities and print some of them
echo "WURFL device id: " . $deviceData->capabilities("wurfl_id") . "<br>";

// print brand & model (static capabilities)
echo "This device is a: " . $deviceData->capabilities("brand_name") . " " . $deviceData->capabilities("model_name") . "<br>";

// check if device is a smartphone (a virtual capability)
if ($deviceData->capabilities("is_smartphone") === "true") {
    echo "This is a smartphone" . "<br>";
} else {
    echo "This is not a smartphone" . "<br>";
}

// Printing all received capabilities
echo "<br>All received capabilities: <br>";
echo "<ul>";
foreach ($deviceData->getAllCapabilities() as $key => $value) {
    echo "<li>$key: $value</li>";
}
echo "</ul>";

// Get all the device manufacturers, and print the first twenty
$limit = 20;

$deviceMakes = $wmClient->getAllDeviceMakes();

echo "Print the first $limit Brand of " . count($deviceMakes) . "<br>";

// Sort the device manufacturer names
sort($deviceMakes);

echo "<ul>";
for ($i = 0; $i < $limit; $i++) {
    echo "<li>" . $deviceMakes[$i] . "</li>";
}
echo "</ul>";

// Now call the WM server to get all device model and marketing names produced by Apple
echo "Print all Model for the Apple Brand" . "<br>";

$modelMktNames = $wmClient->getAllDevicesForMake("Apple");

// Sort $modelMktNames by their model name
array_multisort($modelMktNames);

echo "<ul>";
foreach ($modelMktNames as $modelMktName) {
    echo "<li>" . $modelMktName->modelName() . " " . $modelMktName->marketingName() . "</li>";
}
echo "</ul>";

// Now call the WM server to get all operative system names
$oses = $wmClient->getAllOSes();

echo "Print the list of OSes" . "<br>";

// Sort and print all OS names
sort($oses);

echo "<ul>";
foreach ($oses as $os) {
    echo "<li>" . $os . "</li>";
}
echo "</ul>";

// Let's call the WM server to get all version of the Android OS
echo "Print all versions for the Android OS" . "<br>";

$versions = $wmClient->getAllVersionsForOS("Android");

// Sort all Android version numbers and print them.
sort($versions);

echo "<ul>";
foreach ($versions as $version) {
    echo "<li>" . $version . "</li>";
}
echo "</ul>";
