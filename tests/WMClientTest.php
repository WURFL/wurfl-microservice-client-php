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

namespace ScientiaMobile\WMClient;

use GuzzleHttp\Psr7\Request;
use Prophecy\Argument;

/**
 * Class WMClientTest
 */
class WMClientTest extends \PHPUnit_Framework_TestCase
{
    public function testFactoryInvalidScheme()
    {
        $this->setExpectedException('\InvalidArgumentException');
        WMClient::create('ftp', 'localhost', 80);
    }

    public function testRequestedCapabilities()
    {
        $httpClient = $this->mockHttpClient();
        $client = new WMClient($httpClient->reveal());
        $this->assertTrue($client->hasStaticCapability('brand_name'));
        $this->assertFalse($client->hasStaticCapability('mobile_browser'));
        $this->assertTrue($client->hasVirtualCapability('is_app'));
        $this->assertFalse($client->hasVirtualCapability('invalid_cap'));

        $client->setRequestedCapabilities(['is_app', 'brand_name', 'mobile_browser', 'invalid_cap']);


        $this->assertAttributeEquals(['is_app'], 'requestedVirtualCapabilities', $client);
        $this->assertAttributeEquals(['brand_name'], 'requestedStaticCapabilities', $client);
    }

    public function testLookupDeviceID()
    {
        $httpClient = $this->mockHttpClient();
        $httpClient->post(
            "/v2/lookupdeviceid/json",
            ["User-Agent" => "php-wmclient-api WM-test"],
            Argument::any()
        )
            ->willReturn(ResponseMocker::wmValidDeviceResponse());

        $client = new WMClient($httpClient->reveal());

        $device = $client->lookupDeviceID('samsung_sm_g950f_int_ver1');

        $this->assertSame('samsung_sm_g950f_int_ver1', $device->capabilities('wurfl_id'));
    }

    public function testGetApiVersion()
    {
        $httpClient = $this->mockHttpClient();
        $client = new WMClient($httpClient->reveal());
        $this->assertInternalType('string', $client->getApiVersion());
    }

    /**
     * @return \Prophecy\Prophecy\ObjectProphecy
     */
    private function mockHttpClient()
    {
        $httpClient = $this->prophesize('\ScientiaMobile\WMClient\HttpClient\HttpClientInterface');
        $response = ResponseMocker::wmValidServerInfoResponse();
        $httpClient->getDefaultUserAgent()->willReturn('WM-test');
        $httpClient->get("/v2/getinfo/json", [
            "User-Agent" => "php-wmclient-api WM-test"
        ])->willReturn($response);
        return $httpClient;
    }
}