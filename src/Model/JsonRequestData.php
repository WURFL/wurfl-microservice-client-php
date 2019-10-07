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

class JsonRequestData implements \JsonSerializable
{
    private $data = [
        'lookup_headers' => [],
        'requested_caps' => [],
        'requested_vcaps' => [],
        'wurfl_id' => '',
    ];

    public function lookupHeaders(array $headers)
    {
        $this->data['lookup_headers'] = array_merge($this->data['lookup_headers'], $headers);
    }

    public function requestedCaps(array $requested_static_caps)
    {
        if (!is_array($this->data['requested_caps'])) {
            return [];
        }
        $this->data['requested_caps'] = $requested_static_caps;
    }

    public function requestedVCaps(array $requested_virtual_caps)
    {
        if (!is_array($this->data['requested_vcaps'])) {
            return [];
        }
        return $this->data['requested_vcaps'] = $requested_virtual_caps;
    }

    public function wurflID($deviceId)
    {
        $this->data['wurfl_id'] = $deviceId;
    }

    public function jsonSerialize()
    {
        if (!$this->data['lookup_headers']) {
            $this->data['lookup_headers'] = new \stdClass();
        }
        return $this->data;
    }
}
