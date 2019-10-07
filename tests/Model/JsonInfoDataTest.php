<?php

/*
 * Copyright (c) 2017 ScientiaMobile Inc.
 */

namespace ScientiaMobile\WMClient\Model;

use ScientiaMobile\WMClient\ResponseMocker;

class JsonInfoDataTest extends \PHPUnit_Framework_TestCase
{
    public function testJSONInfoData()
    {
        $response = ResponseMocker::wmValidServerInfoResponse();
        $jsonInfoData = new JsonInfoData($response);
        $this->assertSame("1.0.0.0", $jsonInfoData->wmVersion());
        $this->assertContains("wurfl.zip:for WURFL API 1.9.0.0", $jsonInfoData->wurflInfo());
        $this->assertSame("1.9.0.1", $jsonInfoData->wurflAPIVersion());
        $this->assertGreaterThan(0, count($jsonInfoData->staticCaps()));
        $this->assertGreaterThan(0, count($jsonInfoData->virtualCaps()));
        $this->assertGreaterThan(0, count($jsonInfoData->importantHeaders()));
        $this->assertSame("Thu Sep 18 09:06:28 2017", $jsonInfoData->ltime());

        $serialized = $jsonInfoData->jsonSerialize();
        $this->assertSame('1.9.0.1', $serialized['wurfl_api_version']);
    }
}
