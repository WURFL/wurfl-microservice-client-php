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

interface WMCacheInterface
{

    /**
     * Fetches a value from the cache.
     *
     * @param string $key     The unique key of this item in the cache.
     *
     * @return mixed The value of the item from the cache, or null in case of cache miss.
     */
    public function get($key);

    /**
     * Persists data in the cache, uniquely referenced by a key.
     *
     * @param string                $key   The key of the item to store.
     * @param mixed                 $value The value of the item to store, must be serializable.
     *
     * @return bool True on success and false on failure.
     *
     */
    public function add($key, $value);

    /**
     * Wipes clean the entire cache's keys only for the object cache type
     *
     * @return int The number of items removed from cache
     */
    public function clear();

    /**
     * Returns the namespace prefix
     *
     * @return string The namespace prefix
     */
    public static function getNamespace();
}
