<?php

/*
 * Copyright (c) 2017 ScientiaMobile Inc.
 */

namespace ScientiaMobile\WMClient\Model;

use ScientiaMobile\WMClient\ResponseMocker;

class ModelMktNameDataTest extends \PHPUnit_Framework_TestCase
{
    public function testJSONInfoData()
    {
        $response = ResponseMocker::wmAllDeviceResponse();
        $data = json_decode($response->getBody(), true);
        $makeModels = [];
        foreach ($data as $makeModelData) {
            $makeModels[] = new MakeModelData($makeModelData);
        }

        $this->assertCount(2, $makeModels);

        $this->assertSame('Pro 2', $makeModels[0]->modelName());
        $this->assertSame('', $makeModels[0]->marketingName());


        $this->assertSame('1503-M02', $makeModels[1]->modelName());
        $this->assertSame('360 N4', $makeModels[1]->marketingName());

        $serialized = $makeModels[1]->jsonSerialize();
        $this->assertSame('1503-M02', $serialized['model_name']);
    }
}
