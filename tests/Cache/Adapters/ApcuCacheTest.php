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

namespace ScientiaMobile\WMClient\Cache\Adapters;

class ApcuCacheTest extends CacheTestCase
{
    public static function setUpBeforeClass()
    {
        try {
            ApcuCache::isAvailable();
        } catch (\Exception $e) {
            \PHPUnit_Framework_TestCase::markTestSkipped($e->getMessage());
        }
    }

    protected function setUp()
    {
        parent::setUp();
        $this->cache = new ApcuCache();
        \ini_set('apc.use_request_time', 0);
    }

    public function testCacheAdapterWithTtl()
    {
        \PHPUnit_Framework_TestCase::markTestSkipped("The APCu cache TTL does not expire in a single process/request. See: http://php.net/manual/en/function.apcu-store.php#ttl");
    }
}
