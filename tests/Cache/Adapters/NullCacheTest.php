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

use ScientiaMobile\WMClient\Cache\Adapters\NullCache;
use ScientiaMobile\WMClient\Cache\Adapters\WMAdapterCacheInterface;

class NullCacheTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var WMAdapterCacheInterface
     */
    protected $cache;

    protected function setUp()
    {
        $this->cache = new NullCache();
    }

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
        $this->assertFalse($cache->has('key'));

        $cache->set('key', 'value', \DateInterval::createFromDateString('1 second'));
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

        $this->assertNull($cache->get('key'));

        $this->assertFalse($cache->has('key'));
        $this->assertTrue($cache->delete('key'));
        $this->assertFalse($cache->has('key'));

        $this->assertSame(0, $cache->clear());
    }
}
