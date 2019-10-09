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

use Psr\Http\Message\RequestInterface;
use ScientiaMobile\WMClient\Cache\Adapters\ApcuCache;
use ScientiaMobile\WMClient\Cache\Adapters\FileCache;
use ScientiaMobile\WMClient\Cache\Adapters\NullCache;
use ScientiaMobile\WMClient\Cache\Adapters\WMAdapterCacheInterface;
use ScientiaMobile\WMClient\Cache\ClientCache;
use ScientiaMobile\WMClient\Cache\DeviceCache;
use ScientiaMobile\WMClient\Cache\UserAgentCache;
use ScientiaMobile\WMClient\HttpClient\HttpClientInterface;
use ScientiaMobile\WMClient\Model\JsonDeviceData;
use ScientiaMobile\WMClient\Model\JsonInfoData;
use ScientiaMobile\WMClient\Model\JsonRequestData;
use ScientiaMobile\WMClient\Model\MakeModelData;
use ScientiaMobile\WMClient\Model\ModelMktNameData;

class WMClient
{

    /**
     * @var HttpClientInterface
     */
    private $client;

    /**
     * @var array
     */
    private $staticCapabilities = [];
    /**
     * @var array
     */
    private $virtualCapabilities = [];
    /**
     * @var array
     */
    private $importantHeaders = [];

    /**
     * @var array
     */
    private $requestedStaticCapabilities = [];
    /**
     * @var array
     */
    private $requestedVirtualCapabilities = [];

    /**
     * @var DeviceCache
     */
    private $deviceCache;

    /**
     * @var UserAgentCache
     */
    private $userAgentCache;

    /**
     * @var ClientCache
     */
    private $clientCache;

    /**
     * WMClient constructor.
     * @internal
     * @param HttpClientInterface $client
     * @throws \Exception
     */
    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
        $this->clientCache = new ClientCache(new FileCache(), WMAdapterCacheInterface::NEVER_EXPIRES);
        $this->deviceCache = new DeviceCache(new NullCache(), null);
        $this->userAgentCache = new UserAgentCache(new NullCache(), $this->importantHeaders, null);

        $data = $this->getInfo();
        $this->staticCapabilities = $data->staticCaps();
        $this->virtualCapabilities = $data->virtualCaps();
        $this->importantHeaders = $data->importantHeaders();
    }

    /**
     * Create the WM Client using the default GuzzleHttpClient
     * @param string $scheme http or https
     * @param string $host
     * @param int $port
     * @param string $path
     * @return WMClient
     * @codeCoverageIgnore
     */
    public static function create($scheme, $host, $port, $path = '')
    {
        if (!in_array($scheme, ['http', 'https'], true)) {
            throw new \InvalidArgumentException('Invalid scheme. Allowed values: https or http');
        }
        $httpClient = HttpClient\GuzzleClient::create($scheme, $host, $port, $path);
        return new self($httpClient);
    }

    /**
     * Returns the API version
     * @return string
     */
    public function getApiVersion()
    {
        return '2.0.0';
    }

    /**
     * Enable cache
     * @param null|int Optional The TTL value of cache items.  If no ttl is supplied (or if the ttl is 0),
     *                           the items will persist until the cache is cleared manually,
     *                           or otherwise fails to exist in the cache (clear, restart, etc.).
     * @codeCoverageIgnore
     */
    public function enableCache($ttl = null)
    {
        try {
            $cache = new ApcuCache();
        } catch (\RuntimeException $e) {
            $cache = new FileCache();
        }

        $this->clientCache = new ClientCache($cache, $ttl);
        $this->deviceCache = new DeviceCache($cache, $ttl);
        $this->userAgentCache = new UserAgentCache($cache, $this->importantHeaders, $ttl);
    }

    /**
     * Wipes clean the client cache.
     * @codeCoverageIgnore
     */
    public function clearCache()
    {
        $this->userAgentCache->clear();
        $this->deviceCache->clear();
        $this->clientCache->clear();
    }

    /**
     * Set list of static capabilities to return
     * @param string[] $staticCapabilitiesList
     */
    public function setRequestedStaticCapabilities(array $staticCapabilitiesList)
    {
        $capabilitiesList = [];
        foreach ($staticCapabilitiesList as $capabilityName) {
            if ($this->hasStaticCapability($capabilityName)) {
                $capabilitiesList[] = $capabilityName;
            }
        }
        $this->requestedStaticCapabilities = $capabilitiesList;
    }

    /**
     * Set list of virtual capabilities to return
     * @param string[] $virtualCapabilitiesList
     */
    public function setRequestedVirtualCapabilities(array $virtualCapabilitiesList)
    {
        $capabilitiesList = [];
        foreach ($virtualCapabilitiesList as $capabilityName) {
            if ($this->hasVirtualCapability($capabilityName)) {
                $capabilitiesList[] = $capabilityName;
            }
        }
        $this->requestedVirtualCapabilities = $capabilitiesList;
    }

    /**
     * Set the given capability names to the set they belong
     * @param string[] $capabilitiesList
     */
    public function setRequestedCapabilities(array $capabilitiesList)
    {
        $this->setRequestedStaticCapabilities($capabilitiesList);
        $this->setRequestedVirtualCapabilities($capabilitiesList);
    }

    /**
     * Check if the given $capabilityName exist in this client' static capability set
     * @param string $capabilityName
     * @return bool
     */
    public function hasStaticCapability($capabilityName)
    {
        return in_array($capabilityName, $this->staticCapabilities);
    }

    /**
     * Check if the given $capabilityName exist in this client' virtual capability set
     * @param string $capabilityName
     * @return bool
     */
    public function hasVirtualCapability($capabilityName)
    {
        return in_array($capabilityName, $this->virtualCapabilities);
    }

    /**
     * Detects a device from a Request
     * @param RequestInterface $request
     * @return JsonDeviceData
     */
    public function lookupRequest(RequestInterface $request)
    {
        $endpoint = "/v2/lookuprequest/json";

        $headers = [];
        foreach ($this->importantHeaders as $importantHeader) {
            if ($request->hasHeader($importantHeader)) {
                $headers[$importantHeader] = $request->getHeaderLine($importantHeader);
            }
        }

        $deviceData = $this->userAgentCache->get($this->userAgentCache->makeKeyFromHeaders($headers));
        if ($deviceData) {
            return $deviceData;
        }


        $jsonRequest = new JsonRequestData();
        $jsonRequest->lookupHeaders($headers);
        $jsonRequest->requestedCaps($this->requestedStaticCapabilities);
        $jsonRequest->requestedVCaps($this->requestedVirtualCapabilities);
        $response = $this->client->post($endpoint, $this->makeHeaders($headers), $jsonRequest);

        $deviceData = new JsonDeviceData($response);

        // check if server WURFL.xml has been updated and, if so, clear caches
        $this->clearCacheIfNeeded($deviceData->ltime());
        $this->userAgentCache->add($this->userAgentCache->makeKeyFromHeaders($headers), $deviceData);

        return $deviceData;
    }

    /**
     * Searches WURFL device data using the given user-agent for detection
     * @param string $userAgent
     * @return JsonDeviceData
     */
    public function lookupUserAgent($userAgent)
    {
        $endpoint = "/v2/lookupuseragent/json";
        $headers = ['User-Agent' => $userAgent];


        $deviceData = $this->userAgentCache->get($this->userAgentCache->makeKeyFromHeaders($headers));
        if ($deviceData) {
            return $deviceData;
        }

        $jsonRequest = new JsonRequestData();
        $jsonRequest->lookupHeaders($headers);
        $jsonRequest->requestedCaps($this->requestedStaticCapabilities);
        $jsonRequest->requestedVCaps($this->requestedVirtualCapabilities);
        $response = $this->client->post($endpoint, $this->makeHeaders($headers), $jsonRequest);

        $deviceData = new JsonDeviceData($response);

        // check if server WURFL.xml has been updated and, if so, clear caches
        $this->clearCacheIfNeeded($deviceData->ltime());
        $this->userAgentCache->add($this->userAgentCache->makeKeyFromHeaders($headers), $deviceData);

        return $deviceData;
    }

    /**
     * Searches WURFL device data using its wurfl_id value
     * @param string $deviceId
     * @return JsonDeviceData
     */
    public function lookupDeviceID($deviceId)
    {
        $endpoint = "/v2/lookupdeviceid/json";

        $deviceData = $this->deviceCache->get($deviceId);
        if ($deviceData) {
            return $deviceData;
        }

        $jsonRequest = new JsonRequestData();
        $jsonRequest->wurflID($deviceId);
        $jsonRequest->requestedCaps($this->requestedStaticCapabilities);
        $jsonRequest->requestedVCaps($this->requestedVirtualCapabilities);
        $response = $this->client->post($endpoint, $this->makeHeaders(), $jsonRequest);

        $deviceData = new JsonDeviceData($response);

        // check if server WURFL.xml has been updated and, if so, clear caches
        $this->clearCacheIfNeeded($deviceData->ltime());
        $this->deviceCache->add($deviceId, $deviceData);

        return $deviceData;
    }

    /**
     * Returns information about the running WM server and API
     * @return JsonInfoData
     */
    public function getInfo()
    {
        $endpoint = '/v2/getinfo/json';
        $response = $this->client->get($endpoint, $this->makeHeaders());

        $deviceInfo = new JsonInfoData($response);
        if (!$deviceInfo->wurflInfo()) {
            throw new \Exception("Server returned empty data or a wrong json format");
        }

        // check if server WURFL.xml has been updated and, if so, clear caches
        $this->clearCacheIfNeeded($deviceInfo->ltime());

        return $deviceInfo;
    }

    /**
     * Returns an array of all devices brand_name capability
     * @return array
     */
    public function getAllDeviceMakes()
    {
        $deviceMakesMap = $this->getDeviceMakesMap();
        return array_keys($deviceMakesMap);
    }

    /**
     * Returns an array of an aggregate containing model_names + marketing_names for the given Make.
     * @param $make string
     * @throws \Exception
     * @return ModelMktNameData[]
     */
    public function getAllDevicesForMake($make)
    {
        $deviceMakesMap = $this->getDeviceMakesMap();
        if (!isset($deviceMakesMap[$make])) {
            throw new \Exception(sprintf("Error getting data from WM server: %s does not exist", $make));
        }
        return $deviceMakesMap[$make];
    }

    /**
     * @return array ModelMktNameData[]
     */
    private function getDeviceMakesMap()
    {

        $deviceMakesMap = $this->clientCache->get('deviceMakesMap');
        if (is_array($deviceMakesMap) && count($deviceMakesMap) > 0) {
            return $deviceMakesMap;
        }

        $endpoint = '/v2/alldevices/json';
        $response = $this->client->get($endpoint, $this->makeHeaders());
        $data = json_decode($response->getBody(), true);

        $deviceMakesMap = [];
        foreach ($data as $makeModelData) {
            $brand_name = $makeModelData["brand_name"];
            if ($brand_name === "") {
                continue;
            }

            unset($makeModelData["brand_name"]);
            $deviceMakesMap[$brand_name][] = new ModelMktNameData($makeModelData);
        }

        $this->clientCache->add('deviceMakesMap', $deviceMakesMap);
        return $deviceMakesMap;
    }

    /**
     * Returns an array of all devices device_os capabilities.
     * @return array
     */
    public function getAllOSes()
    {
        return array_keys($this->getDeviceOsVerMap());
    }

    /**
     * Returns an array of all devices device_os_version for a given device_os cap.
     * @param $device_os string
     * @return array
     * @throws \Exception
     */
    public function getAllVersionsForOS($device_os)
    {
        $deviceOsVerMap = $this->getDeviceOsVerMap();
        if (!isset($deviceOsVerMap[$device_os])) {
            throw new \Exception(sprintf("Error getting data from WM server: %s does not exist", $device_os));
        }
        return $deviceOsVerMap[$device_os];
    }

    /**
     * @return array
     */
    private function getDeviceOsVerMap()
    {

        $deviceOsVerMap = $this->clientCache->get('deviceOsVerMap');
        if (is_array($deviceOsVerMap) && count($deviceOsVerMap) > 0) {
            return $deviceOsVerMap;
        }

        $endpoint = '/v2/alldeviceosversions/json';
        $response = $this->client->get($endpoint, $this->makeHeaders());
        $data = json_decode($response->getBody(), true);

        $deviceOsVerMap = [];
        foreach ($data as $deviceOsVer) {
            $os = $deviceOsVer["device_os"];
            if ($os === "") {
                continue;
            }

            $deviceOsVerMap[$os][] = $deviceOsVer["device_os_version"];
        }

        $this->clientCache->add('deviceOsVerMap', $deviceOsVerMap);
        return $deviceOsVerMap;
    }

    /**
     * @param array $headers
     * @return array
     */
    private function makeHeaders(array $headers = [])
    {
        $userAgent = 'php-wmclient-api ' . $this->client->getDefaultUserAgent();
                if (isset($headers['User-Agent'])) {
                    $userAgent .= ' ' . $headers['User-Agent'];
                }
                $headers['User-Agent'] = $userAgent;
                return $headers;
    }

    /**
     * If given ltime is different from client internal one, all caches are cleared and client last load time is updated
     * @param $ltime
     * @return bool
     */
    private function clearCacheIfNeeded($ltime)
    {
        $cached_ltime = $this->clientCache->get('ltime');

        if (strlen($ltime) > 0 && $cached_ltime != $ltime) {
            $this->clearCache();
            $this->clientCache->add('ltime', $ltime);
            return true;
        }
        return false;
    }
}
