<?php

/*
 * Copyright (c) 2017 ScientiaMobile Inc.
 */

namespace ScientiaMobile\WMClient\Model;

use ScientiaMobile\WMClient\ResponseMocker;

class JsonDeviceDataTest extends \PHPUnit_Framework_TestCase
{
    public function testValidRequest()
    {
        $response = ResponseMocker::wmValidDeviceResponse();
        $jsonDeviceData = new JsonDeviceData($response);
        $this->assertSame("1.0.0.0", $jsonDeviceData->apiVersion());
        $this->assertSame(1506605695, $jsonDeviceData->mtime());
        $this->assertSame("Thu Sep 18 09:06:28 2017", $jsonDeviceData->ltime());
        $this->assertEmpty($jsonDeviceData->error());
        $this->assertSame("2960", $jsonDeviceData->capabilities('resolution_height'));
        $this->assertSame("", $jsonDeviceData->capabilities('is_app'));
        $this->assertCount(2, $jsonDeviceData->getAllCapabilities());

        $serialized = $jsonDeviceData->jsonSerialize();
        $this->assertSame("1.0.0.0", $serialized['apiVersion']);
    }

    public function testInvalidRequest()
    {
        $response = ResponseMocker::wmInvalidDeviceResponse();
        $jsonDeviceData = new JsonDeviceData($response);
        $this->assertSame("1.0.0.0", $jsonDeviceData->apiVersion());
        $this->assertSame(1506605733, $jsonDeviceData->mtime());
        $this->assertSame("", $jsonDeviceData->ltime());
        $this->assertSame("error getting device using WURFL device ID Missing device in device definition database: <>", $jsonDeviceData->error());
        $this->assertSame("", $jsonDeviceData->capabilities('resolution_height'));
        $this->assertSame("", $jsonDeviceData->capabilities('is_app'));
        $this->assertEmpty($jsonDeviceData->getAllCapabilities());
        $this->assertSame([], $jsonDeviceData->getAllCapabilities());
    }
}
