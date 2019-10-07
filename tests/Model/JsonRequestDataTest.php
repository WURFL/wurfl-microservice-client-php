<?php

/*
 * Copyright (c) 2017 ScientiaMobile Inc.
 */

namespace ScientiaMobile\WMClient\Model;

class JsonRequestDataTest extends \PHPUnit_Framework_TestCase
{
    public function testShouldAddLookupHeaders()
    {
        $request = new JsonRequestData();
        $request->lookupHeaders(["User-Agent" => "test"]);
        $expected = '{
    "lookup_headers": {
        "User-Agent": "test"
    },
    "requested_caps": [],
    "requested_vcaps": [],
    "wurfl_id": ""
}';
        $this->assertSame($expected, json_encode($request, JSON_PRETTY_PRINT));
    }

    public function testShouldAddRequestedCaps()
    {
        $request = new JsonRequestData();
        $request->requestedCaps(["brand_name", "model_name"]);
        $expected = '{
    "lookup_headers": {},
    "requested_caps": [
        "brand_name",
        "model_name"
    ],
    "requested_vcaps": [],
    "wurfl_id": ""
}';
        $this->assertSame($expected, json_encode($request, JSON_PRETTY_PRINT));
    }

    public function testShouldAddRequestedVCaps()
    {
        $request = new JsonRequestData();
        $request->requestedVCaps(["is_app"]);
        $expected = '{
    "lookup_headers": {},
    "requested_caps": [],
    "requested_vcaps": [
        "is_app"
    ],
    "wurfl_id": ""
}';

        $this->assertSame($expected, json_encode($request, JSON_PRETTY_PRINT));
    }

    public function testShouldAddWurflId()
    {
        $request = new JsonRequestData();
        $request->wurflID("generic");
        $expected = '{
    "lookup_headers": {},
    "requested_caps": [],
    "requested_vcaps": [],
    "wurfl_id": "generic"
}';
        $this->assertSame($expected, json_encode($request, JSON_PRETTY_PRINT));
    }
}
