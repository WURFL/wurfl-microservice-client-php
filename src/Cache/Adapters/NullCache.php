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

class NullCache implements WMAdapterCacheInterface
{

    /**
     * @inheritdoc
     */
    public function setNamespace($namespace)
    {
    }

    /**
     * @inheritdoc
     */
    public function get($key, $default = null)
    {
        return $default;
    }

    /**
     * @inheritdoc
     */
    public function set($key, $value, $ttl = null)
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function delete($key)
    {
        return true;
    }

    /**
     * @inheritdoc
     * @codeCoverageIgnore
     */
    public function getMultiple($keys, $default = null)
    {
        throw new \Exception('Not implemented');
    }

    /**
     * @inheritdoc
     * @codeCoverageIgnore
     */
    public function setMultiple($values, $ttl = null)
    {
        throw new \Exception('Not implemented');
    }

    /**
     * @inheritdoc
     * @codeCoverageIgnore
     */
    public function deleteMultiple($keys)
    {
        throw new \Exception('Not implemented');
    }

    /**
     * @inheritdoc
     */
    public function has($key)
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function clear()
    {
        $removedItems = 0;
        return $removedItems;
    }
}
