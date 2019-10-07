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

class CacheTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var WMAdapterCacheInterface
     */
    protected $cache;

    public function testCacheAdapter()
    {
        $cache = $this->cache;
        $this->assertions($cache);
    }

    public function testCacheAdapterWithNamespace()
    {
        $cache = $this->cache;
        $cache->setNamespace('test');
        $this->assertions($cache);
    }

    public function testCacheAdapterWithTtl()
    {
        $cache = $this->cache;
        $cache->set('key', 'value', 1);
        $this->assertTrue($cache->has('key'));
        sleep(1);
        $this->assertFalse($cache->has('key'));

        $cache->set('key', 'value', \DateInterval::createFromDateString('1 second'));
        $this->assertTrue($cache->has('key'));
        sleep(1);
        $this->assertFalse($cache->has('key'));
    }

    /**
     * @param $cache
     */
    protected function assertions($cache)
    {
        $this->assertNull($cache->get('key'));
        $this->assertSame('default', $cache->get('key', 'default'));

        $cache->set('key', 'value');
        $cache->set('key1', 'value1');
        $cache->set('key2', 'value2');

        $this->assertSame('value', $cache->get('key'));
        $this->assertSame('value1', $cache->get('key1'));
        $this->assertSame('value2', $cache->get('key2'));

        $this->assertTrue($cache->has('key2'));
        $this->assertTrue($cache->delete('key2'));
        $this->assertFalse($cache->has('key2'));

        $this->assertTrue($cache->has('key1'));
        $this->assertSame(2, $cache->clear());
        $this->assertFalse($cache->has('key'));
        $this->assertSame(0, $cache->clear());
    }
}
