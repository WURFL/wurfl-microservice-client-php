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

use PHPUnit\Framework\TestCase;
use ScientiaMobile\WMClient\HttpClient\HttpClientInterface;
use ScientiaMobile\WMClient\Model\JsonRequestData;

class WMClientTest extends TestCase
{
    public function testFactoryInvalidScheme()
    {
        $this->expectException(\InvalidArgumentException::class);
        WMClient::create('ftp', 'localhost', 80);
    }

    public function testRequestedCapabilities()
    {
        $httpClient = $this->mockHttpClient();
        $client = new WMClient($httpClient);
        $this->assertTrue($client->hasStaticCapability('brand_name'));
        $this->assertFalse($client->hasStaticCapability('mobile_browser'));
        $this->assertTrue($client->hasVirtualCapability('is_app'));
        $this->assertFalse($client->hasVirtualCapability('invalid_cap'));

        $client->setRequestedCapabilities(['is_app', 'brand_name', 'mobile_browser', 'invalid_cap']);

        $ref = new \ReflectionProperty($client, 'requestedVirtualCapabilities');
        $ref->setAccessible(true);
        $this->assertSame(['is_app'], $ref->getValue($client));

        $ref = new \ReflectionProperty($client, 'requestedStaticCapabilities');
        $ref->setAccessible(true);
        $this->assertSame(['brand_name'], $ref->getValue($client));
    }

    public function testLookupDeviceID()
    {
        $httpClient = $this->mockHttpClient();
        $httpClient->method('post')->willReturn(ResponseMocker::wmValidDeviceResponse());

        $client = new WMClient($httpClient);

        $device = $client->lookupDeviceID('samsung_sm_g950f_int_ver1');

        $this->assertSame('samsung_sm_g950f_int_ver1', $device->capabilities('wurfl_id'));
    }

    public function testGetApiVersion()
    {
        $httpClient = $this->mockHttpClient();
        $client = new WMClient($httpClient);
        $this->assertIsString($client->getApiVersion());
    }

    /**
     * @return HttpClientInterface&\PHPUnit\Framework\MockObject\MockObject
     */
    private function mockHttpClient()
    {
        $httpClient = $this->createMock(HttpClientInterface::class);
        $response = ResponseMocker::wmValidServerInfoResponse();
        $httpClient->method('getDefaultUserAgent')->willReturn('WM-test');
        $httpClient->method('get')->willReturn($response);
        return $httpClient;
    }
}
