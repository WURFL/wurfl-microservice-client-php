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

namespace ScientiaMobile\WMClient\Integration;

use GuzzleHttp\Psr7\Request;
use ScientiaMobile\WMClient\HttpClient\HttpClientException;
use ScientiaMobile\WMClient\WMClient;

/**
 * Class WMClientTest
 */
class WMClientTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $client = $this->makeTestClient();
        $this->assertInstanceOf('\ScientiaMobile\WMClient\WMClient', $client);
        $this->assertTrue($client->hasVirtualCapability('is_app'));
        $this->assertFalse($client->hasVirtualCapability('invalid'));
        $this->assertTrue($client->hasStaticCapability('brand_name'));
        $this->assertFalse($client->hasStaticCapability('invalid'));
    }

    public function testCreateWithEmptyServerValues()
    {
        $this->setExpectedException("\Exception");
        WMClient::create('http', '', '', '');
    }

    public function testCreateWithEmptyScheme()
    {
        $this->setExpectedException("\InvalidArgumentException", "Invalid scheme. Allowed values: https or http");
        WMClient::create('', 'localhost', 80);
    }

    public function testCreateWithInvalidScheme()
    {
        $this->setExpectedException("\InvalidArgumentException", "Invalid scheme. Allowed values: https or http");
        WMClient::create('ftp', 'localhost', 80);
    }

    public function testCreateWithInvalidHost()
    {
        $this->setExpectedException("\InvalidArgumentException");
        WMClient::create('http', 10, 80);
    }

    public function testCreateWithInvalidPort()
    {
        $this->setExpectedException("\Exception");
        WMClient::create('http', '', 0);
    }

    public function testCreateWithInvalidServer()
    {
        $this->setExpectedException("\ScientiaMobile\WMClient\HttpClient\HttpClientException");
        WMClient::create('http', 'invalid', 333);
    }

    public function testHasStaticCapability()
    {
        $client = $this->makeTestClient();
        $this->assertTrue($client->hasStaticCapability("brand_name"));
        $this->assertTrue($client->hasStaticCapability("model_name"));
        $this->assertTrue($client->hasStaticCapability("is_wireless_device"));
        // this is a virtual capability, so it shouldn't be returned
        $this->assertFalse($client->hasStaticCapability("is_app"));
    }

    public function testHasVirtualCapability()
    {
        $client = $this->makeTestClient();
        $this->assertTrue($client->HasVirtualCapability("is_app"));
        $this->assertTrue($client->HasVirtualCapability("is_smartphone"));
        $this->assertTrue($client->HasVirtualCapability("form_factor"));
        $this->assertTrue($client->HasVirtualCapability("is_app_webview"));
        // this is a static capability, so it shouldn't be returned
        $this->assertFalse($client->HasVirtualCapability("brand_name"));
        $this->assertFalse($client->HasVirtualCapability("is_wireless_device"));
    }

    public function testGetInfo()
    {
        $client = $this->makeTestClient();
        $deviceData = $client->getInfo();
        $this->assertGreaterThan(0, $deviceData->importantHeaders());
        $this->assertGreaterThan(0, $deviceData->staticCaps());
        $this->assertGreaterThan(0, $deviceData->virtualCaps());
        $this->assertNotEmpty($deviceData->wurflAPIVersion());
        $this->assertNotEmpty($deviceData->wmVersion());
        $this->assertNotEmpty($deviceData->wurflInfo());
    }

    public function testLookupUserAgent()
    {
        $client = $this->makeTestClient();
        $ua = "Mozilla/5.0 (Linux; Android 7.0; SAMSUNG SM-G950F Build/NRD90M) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/5.2 Chrome/51.0.2704.106 Mobile Safari/537.36";
        $deviceData = $client->lookupUserAgent($ua);
        $this->assertSame('SM-G950F', $deviceData->capabilities('model_name'));
        $this->assertSame('false', $deviceData->capabilities('is_app'));
        $this->assertSame('false', $deviceData->capabilities('is_app_webview'));

        $mtime = $deviceData->mtime();

        //Test client is not using cache
        sleep(1);
        $deviceData = $client->lookupUserAgent($ua);
        $this->assertNotSame($mtime, $deviceData->mtime());
    }

    public function testLookupUserAgentWithCache()
    {
        $client = $this->makeTestClientWithCache();
        $ua = "Mozilla/5.0 (Linux; Android 7.0; SAMSUNG SM-G950F Build/NRD90M) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/5.2 Chrome/51.0.2704.106 Mobile Safari/537.36";
        $deviceData = $client->lookupUserAgent($ua);
        $this->assertSame('SM-G950F', $deviceData->capabilities('model_name'));
        $this->assertSame('false', $deviceData->capabilities('is_app'));
        $this->assertSame('false', $deviceData->capabilities('is_app_webview'));

        $mtime = $deviceData->mtime();

        sleep(1);
        $deviceData = $client->lookupUserAgent($ua);
        $this->assertSame($mtime, $deviceData->mtime());
        $this->assertSame('SM-G950F', $deviceData->capabilities('model_name'));
        $this->assertSame('false', $deviceData->capabilities('is_app'));
        $this->assertSame('false', $deviceData->capabilities('is_app_webview'));
    }

    public function testLookupEmptyUserAgent()
    {
        $client = $this->makeTestClient();
        $deviceData = $client->lookupUserAgent("");
        // Applicative error is empty, we'll receive an error message in JSON payload
        $this->assertContains('No User-Agent', $deviceData->error());
    }

    public function testLookupUserAgentWithSpecifiedCaps()
    {
        $client = $this->makeTestClient();
        $client->setRequestedStaticCapabilities(["brand_name", "is_wireless_device", "pointing_method", "model_name"]);
        $ua = "Mozilla/5.0 (Nintendo Switch; WebApplet) AppleWebKit/601.6 (KHTML, like Gecko) NF/4.0.0.5.9 NintendoBrowser/5.1.0.13341";
        $deviceData = $client->lookupUserAgent($ua);
        $this->assertSame('Nintendo', $deviceData->capabilities('brand_name'));
        $this->assertSame('Switch', $deviceData->capabilities('model_name'));
        $this->assertSame('touchscreen', $deviceData->capabilities('pointing_method'));
    }


    public function testLookupRequestWithSpecifiedCaps()
    {
        $client = $this->makeTestClient();
        $client->setRequestedStaticCapabilities(["brand_name", "is_wireless_device", "pointing_method", "model_name"]);

        $url = "http://vimeo.com/api/v2/brad/info.json";
        $headers = [
            "Content-Type" => "application/json",
            "Accept-Encoding" => "gzip, deflate",
            "User-Agent" => "Mozilla/5.0 (Nintendo Switch; WebApplet) AppleWebKit/601.6 (KHTML, like Gecko) NF/4.0.0.5.9 NintendoBrowser/5.1.0.13341",
        ];
        $request = new Request("GET", $url, $headers);
        $deviceData = $client->lookupRequest($request);
        $this->assertSame('Nintendo', $deviceData->capabilities('brand_name'));
        $this->assertSame('Switch', $deviceData->capabilities('model_name'));
        $this->assertSame('touchscreen', $deviceData->capabilities('pointing_method'));

        $mtime = $deviceData->mtime();

        //Test client is not using cache
        sleep(1);
        $deviceData = $client->lookupRequest($request);
        $this->assertNotSame($mtime, $deviceData->mtime());
    }


    public function testLookupRequestWithSpecifiedCapsWithCache()
    {
        $client = $this->makeTestClientWithCache();
        $client->setRequestedStaticCapabilities(["brand_name", "is_wireless_device", "pointing_method", "model_name"]);

        $url = "http://vimeo.com/api/v2/brad/info.json";
        $headers = [
            "Content-Type" => "application/json",
            "Accept-Encoding" => "gzip, deflate",
            "User-Agent" => "Mozilla/5.0 (Nintendo Switch; WebApplet) AppleWebKit/601.6 (KHTML, like Gecko) NF/4.0.0.5.9 NintendoBrowser/5.1.0.13341",
        ];
        $request = new Request("GET", $url, $headers);
        $deviceData = $client->lookupRequest($request);
        $this->assertSame('Nintendo', $deviceData->capabilities('brand_name'));
        $this->assertSame('Switch', $deviceData->capabilities('model_name'));
        $this->assertSame('touchscreen', $deviceData->capabilities('pointing_method'));

        $mtime = $deviceData->mtime();


        sleep(1);
        $deviceData = $client->lookupRequest($request);
        $this->assertSame($mtime, $deviceData->mtime());
        $this->assertSame('Nintendo', $deviceData->capabilities('brand_name'));
        $this->assertSame('Switch', $deviceData->capabilities('model_name'));
        $this->assertSame('touchscreen', $deviceData->capabilities('pointing_method'));
    }
    
    public function testLookupRequestWithNoHeaders()
    {
        $client = $this->makeTestClient();
        $client->setRequestedStaticCapabilities(["brand_name", "is_wireless_device", "pointing_method", "model_name"]);

        $url = "http://vimeo.com/api/v2/brad/info.json";
        $headers = [];
        $request = new Request("GET", $url, $headers);
        $deviceData = $client->lookupRequest($request);
        $this->assertContains('No User-Agent', $deviceData->error());
    }

    public function testLookupDeviceId()
    {
        $client = $this->makeTestClient();
        $deviceData = $client->lookupDeviceID("nokia_generic_series40");
        $this->assertSame('1', $deviceData->capabilities('xhtml_support_level'));
        $this->assertSame('128', $deviceData->capabilities('resolution_width'));

        $mtime = $deviceData->mtime();

        //Test client is not using cache
        sleep(1);
        $deviceData = $client->lookupDeviceID("nokia_generic_series40");
        $this->assertNotSame($mtime, $deviceData->mtime());
    }

    public function testLookupDeviceIdWithCache()
    {
        $client = $this->makeTestClientWithCache();
        $deviceData = $client->lookupDeviceID("nokia_generic_series40");
        $this->assertSame('1', $deviceData->capabilities('xhtml_support_level'));
        $this->assertSame('128', $deviceData->capabilities('resolution_width'));

        $mtime = $deviceData->mtime();
        sleep(1);
        $deviceData = $client->lookupDeviceID("nokia_generic_series40");
        $this->assertSame($mtime, $deviceData->mtime());
        $this->assertSame('1', $deviceData->capabilities('xhtml_support_level'));
        $this->assertSame('128', $deviceData->capabilities('resolution_width'));
    }

    public function testLookupDeviceIdWithSpecificCaps()
    {
        $client = $this->makeTestClient();
        $client->setRequestedStaticCapabilities(["brand_name", "is_wireless_device"]);
        $deviceData = $client->lookupDeviceID("generic_opera_mini_version1");
        $this->assertSame('Opera', $deviceData->capabilities('brand_name'));
        $this->assertSame('true', $deviceData->capabilities('is_wireless_device'));
        $this->assertSame('', $deviceData->capabilities('resolution_width'));
    }

    public function testLookupDeviceIdWithWrongId()
    {
        $client = $this->makeTestClient();
        $deviceData = $client->lookupDeviceID("nokia_generic_series40_wrong");
        $this->assertNotNull($deviceData->apiVersion());
        $this->assertNotEmpty($deviceData->error());
        $this->assertGreaterThan(0, $deviceData->mtime());
    }

    public function testLookupDeviceIdWithEmptyId()
    {
        $client = $this->makeTestClient();
        $deviceData = $client->lookupDeviceID("");
        $this->assertNotNull($deviceData->apiVersion());
        $this->assertNotEmpty($deviceData->error());
        $this->assertGreaterThan(0, $deviceData->mtime());
    }

    public function testGetAllMakeModel()
    {
        $client = $this->makeTestClient();
        $makeModels = $client->getAllMakeModel();
        $this->assertGreaterThan(20000, count($makeModels));
        $this->assertNotEmpty($makeModels[0]->brandName());
        $this->assertNotEmpty($makeModels[0]->modelName());
        $this->assertEmpty($makeModels[0]->marketingName());
    }

    public function testGetAllMakeModelWithCache()
    {
        $client = $this->makeTestClientWithCache();
        $makeModels = $client->getAllMakeModel();
        $this->assertGreaterThan(20000, count($makeModels));
        $this->assertNotEmpty($makeModels[0]->brandName());
        $this->assertNotEmpty($makeModels[0]->modelName());
        $this->assertEmpty($makeModels[0]->marketingName());
        
        $makeModels = $client->getAllMakeModel();
        $this->assertGreaterThan(20000, count($makeModels));
        $this->assertNotEmpty($makeModels[0]->brandName());
        $this->assertNotEmpty($makeModels[0]->modelName());

        $this->assertEmpty($makeModels[0]->marketingName());
    }

    public function testGetAllDeviceMakes()
    {
        $client = $this->makeTestClient();
        $modelMktName = $client->getAllDeviceMakes();
        $this->assertGreaterThan(2000, count($modelMktName));
        $this->assertNotEmpty($modelMktName[0]);
    }

    public function testGetAllDevicesForMake()
    {
        $client = $this->makeTestClient();
        $modelMktName = $client->getAllDevicesForMake("Nokia");
        $this->assertNotEmpty($modelMktName[0]->modelName());
        $this->assertEmpty($modelMktName[0]->marketingName());
        $this->assertGreaterThan(700, count($modelMktName));

        $this->expectException("\Exception");
        $client->getAllDevicesForMake("Invalid");
    }

    public function testGetAllOses()
    {
        $client = $this->makeTestClient();
        $modelMktName = $client->getAllOSes();
        $this->assertGreaterThan(20, count($modelMktName));
        $this->assertNotEmpty($modelMktName[0]);
    }

    public function testGetAllVersionsForOS()
    {
        $client = $this->makeTestClient();
        $modelMktName = $client->getAllVersionsForOS("Android");
        $this->assertGreaterThan(30, count($modelMktName));

        $this->expectException("\Exception");
        $client->getAllDevicesForMake("NotExistingOs");
    }

    /**
     * @return WMClient
     */
    private function makeTestClient()
    {
        $scheme = getenv('WM_SCHEME');
        $host = getenv('WM_HOST');
        $port = getenv('WM_PORT');
        $client = null;

        try {
            $client = WMClient::create($scheme, $host, $port);
        } catch (HttpClientException $e) {
            self::markTestSkipped(
                sprintf('Failed to connect with WM Server on %s://%s:%s', $scheme, $host, $port)
            );
        }
        return $client;
    }

    private function makeTestClientWithCache()
    {
        $client = $this->makeTestClient();
        $client->enableCache();
        return $client;
    }
}
