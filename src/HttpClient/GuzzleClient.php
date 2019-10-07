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

namespace ScientiaMobile\WMClient\HttpClient;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\UriInterface;

class GuzzleClient implements HttpClientInterface
{
    /**
     * @var UriInterface
     */
    private $uri;

    /**
     * @var \GuzzleHttp\ClientInterface
     */
    private $client;

    /**
     * GuzzleHttpClient constructor.
     * @param Client $guzzleClient
     * @param UriInterface $baseUri
     * @codeCoverageIgnore
     */
    public function __construct(Client $guzzleClient, UriInterface $baseUri)
    {
        $this->uri = $baseUri;
        $this->client = $guzzleClient;
    }

    /**
     * @param string $scheme
     * @param string $host
     * @param int $port
     * @param string $path
     * @return GuzzleClient
     * @codeCoverageIgnore
     */
    public static function create($scheme, $host, $port, $path)
    {
        $uri = Uri::fromParts([
            'scheme' => $scheme,
            'host' => $host,
            'port' => $port,
            'path' => $path
        ]);

        return new self(new Client(), $uri);
    }

    /**
     * @param string $endpoint
     * @param array $headers
     * @return \GuzzleHttp\Psr7\Response
     */
    public function get($endpoint, $headers)
    {
        $uri = $this->uri->withPath($endpoint);
        $options = [];
        if ($headers) {
            $options['headers'] = $headers;
        }

        try {
            $response = $this->client->get($uri, $options);
        } catch (\Exception $e) {
            throw new HttpClientException($e->getMessage(), $e->getCode(), $e);
        }

        return $response;
    }

    /**
     * @param string $endpoint
     * @param array $headers
     * @param array $json_body
     * @return \GuzzleHttp\Psr7\Response
     */
    public function post($endpoint, $headers, $json_body)
    {
        $uri = $this->uri->withPath($endpoint);
        $options = [];
        if ($headers) {
            $options['headers'] = $headers;
        }

        if ($json_body) {
            $options['json'] = $json_body;
        }

        try {
            $response = $this->client->post($uri, $options);
        } catch (\Exception $e) {
            throw new HttpClientException($e);
        }

        return $response;
    }

    /**
     * @return string
     */
    public function getDefaultUserAgent()
    {
        return \GuzzleHttp\default_user_agent();
    }
}
