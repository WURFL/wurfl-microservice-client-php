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

interface HttpClientInterface
{
    /**
     * Send a GET request
     * @param string $endpoint
     * @param array $headers
     * @throws HttpClientException
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function get($endpoint, $headers);

    /**
     * Send a POST request
     * @param string $endpoint
     * @param array $headers
     * @param \ScientiaMobile\WMClient\Model\JsonRequestData $jsonRequestData
     * @throws HttpClientException
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function post($endpoint, $headers, $jsonRequestData);

    /**
     * Get the default User-Agent string to use with the client
     * @return string
     */
    public function getDefaultUserAgent();
}
