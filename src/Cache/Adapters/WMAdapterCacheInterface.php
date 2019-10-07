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

use Psr\SimpleCache\CacheInterface;

interface WMAdapterCacheInterface extends CacheInterface
{
    const NEVER_EXPIRES = 0;
    const EXPIRES_AFTER_ONE_HOUR = 3600;
    const EXPIRES_AFTER_ONE_DAY = 86400;
    const EXPIRES_AFTER_ONE_WEEK = 604800;
    const EXPIRES_AFTER_ONE_MONTH = 2592000;
    const EXPIRES_AFTER_ONE_YEAR = 31556926;

    /**
     * Set a namespace for the cache
     *
     * @param string $namespace
     */
    public function setNamespace($namespace);
}
