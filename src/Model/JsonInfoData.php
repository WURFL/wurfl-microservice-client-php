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

class JsonInfoData implements \JsonSerializable
{
    private $data = [
        'wurfl_api_version' => '',
        'wurfl_info' => '',
        'wm_version' => '',
        'important_headers' => [],
        'static_caps' => [],
        'virtual_caps' => [],
        'ltime' => '',
    ];

    /**
     * JSONInfoData constructor.
     * @param ResponseInterface $response
     */
    public function __construct(ResponseInterface $response)
    {
        $data = json_decode($response->getBody(), true);
        $this->data = array_merge($this->data, $data);
    }

    public function wurflAPIVersion()
    {
        return $this->data['wurfl_api_version'];
    }

    public function wurflInfo()
    {
        return $this->data['wurfl_info'];
    }

    public function wmVersion()
    {
        return $this->data['wm_version'];
    }

    public function importantHeaders()
    {
        if (!is_array($this->data['important_headers'])) {
            return [];
        }
        return $this->data['important_headers'];
    }

    public function staticCaps()
    {
        if (!is_array($this->data['static_caps'])) {
            return [];
        }
        return $this->data['static_caps'];
    }

    public function virtualCaps()
    {
        if (!is_array($this->data['virtual_caps'])) {
            return [];
        }
        return $this->data['virtual_caps'];
    }

    public function ltime()
    {
        return $this->data['ltime'];
    }

    public function jsonSerialize()
    {
        return $this->data;
    }
}
