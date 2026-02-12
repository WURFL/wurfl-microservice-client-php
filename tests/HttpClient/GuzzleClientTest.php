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

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use PHPUnit\Framework\TestCase;
use ScientiaMobile\WMClient\HttpClient\GuzzleClient;
use ScientiaMobile\WMClient\Model\JsonRequestData;

class GuzzleClientTest extends TestCase
{
    public function testGetMethod()
    {
        $mock = new MockHandler([
            new Response(200, [], 'body response'),
        ]);
        $client = $this->makeClient($mock);
        $response = $client->get('/endpoint', []);
        $this->assertInstanceOf('Psr\Http\Message\ResponseInterface', $response);
    }

    public function testGetMethodWithHeaders()
    {
        $mock = new MockHandler([
            new Response(200, [], 'body response'),
        ]);
        $client = $this->makeClient($mock);
        $response = $client->get('/endpoint', ['User-Agent' => 'Mozilla']);
        $this->assertInstanceOf('Psr\Http\Message\ResponseInterface', $response);
    }

    public function testGetMethodException()
    {
        $mock = new MockHandler([
            new RequestException('Error', new Request('GET', '/endpoint')),
        ]);
        $client = $this->makeClient($mock);

        $this->expectException('\ScientiaMobile\WMClient\HttpClient\HttpClientException');
        $client->get('/endpoint', []);
    }

    public function testPostMethod()
    {
        $mock = new MockHandler([
            new Response(200, [], 'body response'),
        ]);
        $client = $this->makeClient($mock);
        $response = $client->post('/endpoint', [], []);
        $this->assertInstanceOf('Psr\Http\Message\ResponseInterface', $response);
    }

    public function testPostMethodException()
    {
        $mock = new MockHandler([
            new RequestException('Error', new Request('POST', '/endpoint')),
        ]);
        $client = $this->makeClient($mock);

        $this->expectException('\ScientiaMobile\WMClient\HttpClient\HttpClientException');
        $client->post('/endpoint', [], []);
    }

    public function testPostMethodWithPayload()
    {
        $mock = new MockHandler([
            new Response(200, [], 'body response'),
        ]);
        $client = $this->makeClient($mock);
        $jsonBody = (new JsonRequestData())->jsonSerialize();
        $response = $client->post('/endpoint', [], $jsonBody);
        $this->assertInstanceOf('Psr\Http\Message\ResponseInterface', $response);
    }

    public function testPostMethodWithPayloadAndHeaders()
    {
        $mock = new MockHandler([
            new Response(200, [], 'body response'),
        ]);
        $client = $this->makeClient($mock);
        $jsonBody = (new JsonRequestData())->jsonSerialize();
        $headers = ['User-Agent' => 'Mozilla'];
        $response = $client->post('/endpoint', $headers, $jsonBody);
        $this->assertInstanceOf('Psr\Http\Message\ResponseInterface', $response);
    }

    public function testGetDefaultUserAgent()
    {
        $mock = new MockHandler([]);
        $client = $this->makeClient($mock);
        $this->assertStringStartsWith('GuzzleHttp/', $client->getDefaultUserAgent());
    }

    private function makeClient(MockHandler $mock): GuzzleClient
    {
        $handlerStack = HandlerStack::create($mock);
        $guzzle = new Client(['handler' => $handlerStack]);
        $uri = new Uri('http://test.local');
        return new GuzzleClient($guzzle, $uri);
    }
}
