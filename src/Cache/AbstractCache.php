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

use ScientiaMobile\WMClient\Cache\Adapters\WMAdapterCacheInterface;

abstract class AbstractCache implements WMCacheInterface
{
    /**
     * @var WMAdapterCacheInterface
     */
    protected $cache;

    /**
     * @var int|null
     */
    protected $ttl;

    /**
     * UserAgentCache constructor.
     * @param WMAdapterCacheInterface $cache
     * @param null|int $ttl
     */
    public function __construct(WMAdapterCacheInterface $cache, $ttl)
    {
        $this->cache = clone $cache;
        $this->cache->setNamespace($this->getNamespace());
        $this->ttl = $ttl;
    }

    /**
     * @inheritdoc
     */
    public function get($key)
    {
        return $this->cache->get($key);
    }

    /**
     * @inheritdoc
     */
    public function add($key, $value)
    {
        return $this->cache->set($key, $value, $this->ttl);
    }

    /**
     * @inheritdoc
     */
    public function clear()
    {
        return $this->cache->clear();
    }
}
