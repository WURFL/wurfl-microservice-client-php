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

namespace ScientiaMobile\WMClient;

use GuzzleHttp\Psr7\Response;

class ResponseMocker
{
    public static function wmValidServerInfoResponse()
    {
        return new Response(200, [], '
{
    "wurfl_api_version": "1.9.0.1",
    "wurfl_info": "Root:/usr/share/wurfl/wurfl.zip:for WURFL API 1.9.0.0 - evaluation, db.scientiamobile.com - 2017-06-15 15:22:29",
    "wm_version": "1.0.0.0",
    "important_headers": [
        "User-Agent",
        "X-UCBrowser-Device-UA",
        "Device-Stock-UA",
        "X-OperaMini-Phone-UA",
        "Accept-Encoding",
        "X-Requested-With"
    ],
    "static_caps": [
        "brand_name",
        "can_assign_phone_number",
        "device_os",
        "device_os_version",
        "is_smarttv",
        "is_tablet",
        "is_wireless_device",
        "marketing_name",
        "mobile_browser_version",
        "model_name",
        "pointing_method",
        "preferred_markup",
        "resolution_height",
        "resolution_width",
        "ux_full_desktop",
        "xhtml_support_level"
    ],
    "virtual_caps": [
        "advertised_app_name",
        "advertised_browser",
        "advertised_browser_version",
        "advertised_device_os",
        "advertised_device_os_version",
        "complete_device_name",
        "device_name",
        "form_factor",
        "is_android",
        "is_app",
        "is_app_webview",
        "is_full_desktop",
        "is_html_preferred",
        "is_ios",
        "is_largescreen",
        "is_mobile",
        "is_phone",
        "is_robot",
        "is_smartphone",
        "is_touchscreen",
        "is_windows_phone",
        "is_wml_preferred",
        "is_xhtmlmp_preferred"
    ],
    "ltime": "Thu Sep 18 09:06:28 2017"
}');
    }

    /**
     * Mock a valid response after a WURFL update
     * @return Response
     */
    public static function wmValidServerInfoResponseWithUpdatedWURFL()
    {
        return new Response(200, [], '
{
    "wurfl_api_version": "1.9.0.1",
    "wurfl_info": "Root:/usr/share/wurfl/wurfl.zip:for WURFL API 1.9.0.0 - evaluation, db.scientiamobile.com - 2017-06-15 15:22:29",
    "wm_version": "1.0.0.0",
    "important_headers": [
        "User-Agent",
        "X-UCBrowser-Device-UA",
        "Device-Stock-UA",
        "X-OperaMini-Phone-UA",
        "Accept-Encoding",
        "X-Requested-With"
    ],
    "static_caps": [
        "brand_name",
        "can_assign_phone_number",
        "device_os",
        "device_os_version",
        "is_smarttv",
        "is_tablet",
        "is_wireless_device",
        "marketing_name",
        "mobile_browser_version",
        "model_name",
        "pointing_method",
        "preferred_markup",
        "resolution_height",
        "resolution_width",
        "ux_full_desktop",
        "xhtml_support_level"
    ],
    "virtual_caps": [
        "advertised_app_name",
        "advertised_browser",
        "advertised_browser_version",
        "advertised_device_os",
        "advertised_device_os_version",
        "complete_device_name",
        "device_name",
        "form_factor",
        "is_android",
        "is_app",
        "is_app_webview",
        "is_full_desktop",
        "is_html_preferred",
        "is_ios",
        "is_largescreen",
        "is_mobile",
        "is_phone",
        "is_robot",
        "is_smartphone",
        "is_touchscreen",
        "is_windows_phone",
        "is_wml_preferred",
        "is_xhtmlmp_preferred"
    ],
    "ltime": "Thu Sep 19 09:06:28 2017"
}');
    }

    public static function wmValidDeviceResponse()
    {
        return new Response(200, [], '
{
    "apiVersion": "1.0.0.0",
    "capabilities": {
        "resolution_height": "2960",
        "wurfl_id": "samsung_sm_g950f_int_ver1"
    },
    "error": "", 
    "ltime": "Thu Sep 18 09:06:28 2017", 
    "mtime": 1506605695
}
');
    }

    public static function wmInvalidDeviceResponse()
    {
        return new Response(200, [], '
{
    "apiVersion": "1.0.0.0", 
    "capabilities": null, 
    "error": "error getting device using WURFL device ID Missing device in device definition database: <>", 
    "ltime": "", 
    "mtime": 1506605733
}
');
    }

    public static function wmAllDeviceResponse()
    {
        return new Response(200, [], '
[
  {
    "brand_name": "2Good",
    "model_name": "Pro 2"
  },
  {
    "brand_name": "QiKU",
    "model_name": "1503-M02",
    "marketing_name": "360 N4"
  }
]
');
    }
}
