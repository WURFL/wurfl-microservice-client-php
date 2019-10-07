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

class FileCacheTest extends CacheTestCase
{
    protected function setUp()
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $this->cache = new FileCache();
    }

    public function testCacheFileWithInvalidData()
    {
        $method = new \ReflectionMethod('ScientiaMobile\WMClient\Cache\Adapters\FileCache', 'getFilePathForKey');
        $method->setAccessible(true);
        $key = 'key';
        $filePathForKey = $method->invoke($this->cache, $key);
        @mkdir(dirname($filePathForKey, 0777, true));
        @touch($filePathForKey);
        $this->assertNull($this->cache->get($key));
    }
}
