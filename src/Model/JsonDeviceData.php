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

namespace ScientiaMobile\WMClient\Model;

use Psr\Http\Message\ResponseInterface;

class JsonDeviceData implements \JsonSerializable
{
    private $data = [
        'apiVersion' => '',
        'capabilities' => [],
        'error' => '',
        'mtime' => '',
        'ltime' => '',
    ];

    /**
     * @param ResponseInterface $response
     */
    public function __construct(ResponseInterface $response)
    {
        $data = json_decode($response->getBody(), true);
        $this->data = array_merge($this->data, $data);
    }

    public function apiVersion()
    {
        return $this->data['apiVersion'];
    }

    /**
     * @param $capability
     * @return string
     */
    public function capabilities($capability)
    {
        if (!is_array($this->data['capabilities'])) {
            return '';
        }
        if (array_key_exists($capability, $this->data['capabilities'])) {
            return $this->data['capabilities'][$capability];
        }
        return '';
    }

    /**
     * @return array
     */
    public function getAllCapabilities()
    {
        if (!$this->data['capabilities']) {
            return [];
        }
        return $this->data['capabilities'];
    }

    /**
     * @return string
     */
    public function error()
    {
        return $this->data['error'];
    }

    /**
     * @return string
     */
    public function mtime()
    {
        return $this->data['mtime'];
    }

    /**
     * @return string
     */
    public function ltime()
    {
        return $this->data['ltime'];
    }

    public function jsonSerialize()
    {
        return $this->data;
    }
}
