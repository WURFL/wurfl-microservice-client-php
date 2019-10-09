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

    public function testLookupRequest()
    {
        $ua = "Mozilla/5.0 (Nintendo Switch; WebApplet) AppleWebKit/601.6 (KHTML, like Gecko) NF/4.0.0.5.9 NintendoBrowser/5.1.0.13341";
        $default_ua = 'php-wmclient-api ' . 'WM-test';
        $expected_request_ua = $default_ua . ' ' . $ua;
        $httpClient = $this->prophesize('\ScientiaMobile\WMClient\HttpClient\HttpClientInterface');
        $response = ResponseMocker::wmValidServerInfoResponse();
        $httpClient->getDefaultUserAgent()->willReturn('WM-test');
        $httpClient->get("/v2/getinfo/json", [
            "User-Agent" => $default_ua,
        ])->willReturn($response);
        $httpClient->post(
            "/v2/lookuprequest/json",
            [
                "User-Agent" => $expected_request_ua,
                "Accept-Encoding" => "gzip, deflate"
            ],
            Argument::any()
        )
            ->willReturn(ResponseMocker::wmValidDeviceResponse());

        $client = new WMClient($httpClient->reveal());
        $url = "http://vimeo.com/api/v2/brad/info.json";
        $headers = [
            "Content-Type" => "application/json",
            "Accept-Encoding" => "gzip, deflate",
            "User-Agent" => $ua,
        ];
        $request = new Request("GET", $url, $headers);

        $client->lookupRequest($request);
    }

    public function testLookupUserAgent()
    {
        $ua = 'Mozilla';
        $default_ua = 'php-wmclient-api ' . 'WM-test';
        $expected_request_ua = $default_ua . ' ' . $ua;
        $httpClient = $this->prophesize('\ScientiaMobile\WMClient\HttpClient\HttpClientInterface');
        $response = ResponseMocker::wmValidServerInfoResponse();
        $httpClient->getDefaultUserAgent()->willReturn('WM-test');
        $httpClient->get("/v2/getinfo/json", [
            "User-Agent" => $default_ua,
        ])->willReturn($response);
        $httpClient->post(
            "/v2/lookupuseragent/json",
            ["User-Agent" => $expected_request_ua],
            Argument::any()
        )
            ->willReturn(ResponseMocker::wmValidDeviceResponse());

        $client = new WMClient($httpClient->reveal());

        $device = $client->lookupUserAgent('Mozilla');
        $this->assertSame('samsung_sm_g950f_int_ver1', $device->capabilities('wurfl_id'));
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

    public function testLookupHeaderMixedCase()
        {
            $ua = "Mozilla/5.0 (Nintendo Switch; WebApplet) AppleWebKit/601.6 (KHTML, like Gecko) NF/4.0.0.5.9 NintendoBrowser/5.1.0.13341";
            $default_ua = 'php-wmclient-api ' . 'WM-test';
            $expected_request_ua = $default_ua . ' ' . $ua;
            $httpClient = $this->prophesize('\ScientiaMobile\WMClient\HttpClient\HttpClientInterface');
            $response = ResponseMocker::wmValidServerInfoResponse();
            $httpClient->getDefaultUserAgent()->willReturn('WM-test');
            $httpClient->get("/v2/getinfo/json", [
                "User-Agent" => $default_ua,
            ])->willReturn($response);
            $httpClient->post(
                "/v2/lookuprequest/json",
                [
                    "User-Agent" => $expected_request_ua,
                    "Accept-Encoding" => "gzip, deflate",
                    "X-UCBrowser-Device-UA" => "Mozilla/5.0 (SAMSUNG; SAMSUNG-GT-S5253/S5253DDJI7; U; Bada/1.0; en-us) AppleWebKit/533.1 (KHTML, like Gecko) Dolfin/2.0 Mobile WQVGA SMM-MMS/1.2.0 OPN-B",
                ],
                Argument::any()
            )
                ->willReturn(ResponseMocker::wmValidDeviceResponse());

            $client = new WMClient($httpClient->reveal());
            $url = "http://vimeo.com/api/v2/brad/info.json";
            $headers = [
                "Content-Type" => "application/json",
                "AccEpt-Encoding" => "gzip, deflate",
                "X-uCBrowser-device-UA" => "Mozilla/5.0 (SAMSUNG; SAMSUNG-GT-S5253/S5253DDJI7; U; Bada/1.0; en-us) AppleWebKit/533.1 (KHTML, like Gecko) Dolfin/2.0 Mobile WQVGA SMM-MMS/1.2.0 OPN-B",
                "user-agent" => $ua,
            ];
            $request = new Request("GET", $url, $headers);

            $client->lookupRequest($request);
        }
}
