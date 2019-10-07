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

namespace ScientiaMobile\WMClient\Cache;

class UserAgentCacheTest extends \PHPUnit_Framework_TestCase
{
    private $importantHeaders = ['User-Agent'];

    public function testMakeKey()
    {
        $adapter = $this->prophesize('\ScientiaMobile\WMClient\Cache\Adapters\WMAdapterCacheInterface');
        $cache = new UserAgentCache($adapter->reveal(), $this->importantHeaders, null);

        $headers = [
            'User-Agent' => 'Mozilla',
            'Accepted-Encoding' => 'gzip'
        ];

        $this->assertSame(\md5('Mozilla'), $cache->makeKeyFromHeaders($headers));
    }

    public function testShouldRetrieveItemFromCache()
    {
        $key = 'key';
        $expected = 'value';

        $adapter = $this->prophesize('\ScientiaMobile\WMClient\Cache\Adapters\WMAdapterCacheInterface');
        $adapter->get($key)->willReturn($expected);
        $adapter->setNamespace(UserAgentCache::getNamespace())->willReturn();
        $cache = new UserAgentCache($adapter->reveal(), $this->importantHeaders, null);

        $this->assertSame($expected, $cache->get($key));
    }


    public function testShouldAddItemToCache()
    {
        $key = 'key';
        $value = 'value';
        $ttl = 10;

        $adapter = $this->prophesize('\ScientiaMobile\WMClient\Cache\Adapters\WMAdapterCacheInterface');
        $adapter->set($key, $value, $ttl)->willReturn(true);
        $adapter->setNamespace(UserAgentCache::getNamespace())->willReturn();
        $cache = new UserAgentCache($adapter->reveal(), $this->importantHeaders, $ttl);

        $this->assertTrue($cache->add($key, $value));
    }

    public function testShouldClearNamespaceCache()
    {
        $adapter = $this->prophesize('\ScientiaMobile\WMClient\Cache\Adapters\WMAdapterCacheInterface');
        $adapter->clear()->willReturn(0);
        $adapter->setNamespace(UserAgentCache::getNamespace())->willReturn();
        $cache = new UserAgentCache($adapter->reveal(), $this->importantHeaders, 0);
        $this->assertSame(0, $cache->clear());
    }
}
