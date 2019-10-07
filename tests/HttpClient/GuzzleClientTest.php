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


use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Uri;
use ScientiaMobile\WMClient\HttpClient\GuzzleClient;
use ScientiaMobile\WMClient\Model\JsonRequestData;

class GuzzleClientTest extends \PHPUnit_Framework_TestCase
{
    public function testGetMethod()
    {
        $uri = new Uri('http://test.local');
        $guzzle = $this->prophesize('\GuzzleHttp\Client');

        $guzzleResponse = new Response(200, [], \GuzzleHttp\Psr7\stream_for('body response'));
        $guzzle->get('http://test.local/endpoint', [])->willReturn($guzzleResponse);

        $client = new GuzzleClient($guzzle->reveal(), $uri);
        $response = $client->get('/endpoint', []);
        $this->assertInstanceOf('Psr\Http\Message\ResponseInterface', $response);
    }

    public function testGetMethodWithHeaders()
    {
        $uri = new Uri('http://test.local');
        $guzzle = $this->prophesize('\GuzzleHttp\Client');
        $headers = ['User-Agent' => 'Mozilla'];

        $guzzleResponse = new Response(200, [], \GuzzleHttp\Psr7\stream_for('body response'));
        $guzzle->get('http://test.local/endpoint', ["headers" => $headers])->willReturn($guzzleResponse);

        $client = new GuzzleClient($guzzle->reveal(), $uri);
        $response = $client->get('/endpoint', $headers);
        $this->assertInstanceOf('Psr\Http\Message\ResponseInterface', $response);
    }

    public function testGetMethodException()
    {
        $uri = new Uri('http://test.local');

        $guzzle = $this->prophesize('\GuzzleHttp\Client');
        $guzzle->get('http://test.local/endpoint', [])->willThrow(new \Exception());

        $client = new GuzzleClient($guzzle->reveal(), $uri);

        $this->setExpectedException('\ScientiaMobile\WMClient\HttpClient\HttpClientException');
        $client->get('/endpoint', []);
    }

    public function testPostMethod()
    {
        $uri = new Uri('http://test.local');
        $guzzleResponse = new Response(200, [], \GuzzleHttp\Psr7\stream_for('body response'));

        $guzzle = $this->prophesize('\GuzzleHttp\Client');
        $guzzle->post('http://test.local/endpoint', [])->willReturn($guzzleResponse);

        $client = new GuzzleClient($guzzle->reveal(), $uri);
        $response = $client->post('/endpoint', [], []);
        $this->assertInstanceOf('Psr\Http\Message\ResponseInterface', $response);
    }

    public function testPostMethodException()
    {
        $uri = new Uri('http://test.local');
        $guzzle = $this->prophesize('\GuzzleHttp\Client');

        $guzzle->post('http://test.local/endpoint', [])->willThrow(new \Exception());

        $client = new GuzzleClient($guzzle->reveal(), $uri);

        $this->setExpectedException('\ScientiaMobile\WMClient\HttpClient\HttpClientException');
        $client->post('/endpoint', [], []);
    }

    public function testPostMethodWithPayload()
    {
        $uri = new Uri('http://test.local');
        $guzzle = $this->prophesize('\GuzzleHttp\Client');
        $jsonBody = (new JsonRequestData())->jsonSerialize();

        $guzzleResponse = new Response(200, [], \GuzzleHttp\Psr7\stream_for('body response'));
        $guzzle->post('http://test.local/endpoint', ['json' => $jsonBody])->willReturn($guzzleResponse);

        $client = new GuzzleClient($guzzle->reveal(), $uri);
        $response = $client->post('/endpoint', [], $jsonBody);
        $this->assertInstanceOf('Psr\Http\Message\ResponseInterface', $response);
    }

    public function testPostMethodWithPayloadAndHeaders()
    {
        $uri = new Uri('http://test.local');
        $guzzle = $this->prophesize('\GuzzleHttp\Client');
        $jsonBody = (new JsonRequestData())->jsonSerialize();
        $headers = ['User-Agent' => 'Mozilla'];

        $guzzleResponse = new Response(200, [], \GuzzleHttp\Psr7\stream_for('body response'));
        $guzzle->post(
            'http://test.local/endpoint',
            ['headers' => $headers, 'json' => $jsonBody]
        )->willReturn($guzzleResponse);

        $client = new GuzzleClient($guzzle->reveal(), $uri);
        $response = $client->post('/endpoint', $headers, $jsonBody);
        $this->assertInstanceOf('Psr\Http\Message\ResponseInterface', $response);
    }

    public function testGetDefaultUserAgent()
    {
        $uri = new Uri('http://test.local');
        $guzzle = $this->prophesize('\GuzzleHttp\Client');
        $client = new GuzzleClient($guzzle->reveal(), $uri);
        $this->assertStringStartsWith('GuzzleHttp/6', $client->getDefaultUserAgent());
    }
}
