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

class MakeModelData implements \JsonSerializable
{
    private $data = [
        'brand_name' => '',
        'model_name' => '',
        'marketing_name' => '',
    ];

    /**
     * MakeModelData constructor.
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = array_merge($this->data, $data);
    }

    public function brandName()
    {
        return $this->data['brand_name'];
    }

    public function modelName()
    {
        return $this->data['model_name'];
    }

    public function marketingName()
    {
        return $this->data['marketing_name'];
    }

    public function jsonSerialize()
    {
        return $this->data;
    }
}
