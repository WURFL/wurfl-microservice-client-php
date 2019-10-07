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

class ApcuCache implements WMAdapterCacheInterface
{
    private $namespace = 'wm_apcu';

    /**
     * ApcCache constructor
     * @throws \RuntimeException
     */
    public function __construct()
    {
        self::isAvailable();
    }

    /**
     * Check if the apcu extension is installed and enabled
     * @throws \RuntimeException
     */
    public static function isAvailable()
    {
        if (!\function_exists('apcu_fetch')) {
            throw new \RuntimeException("The PHP extension apcu must be installed in order to use this adapter.");
        }

        if (!\ini_get('apc.enabled') == true) {
            throw new \RuntimeException("The PHP extension apcu must be enabled in order to use this adapter.");
        }

        if (php_sapi_name() === 'cli' && !\ini_get('apc.enable_cli') == true) {
            throw new \RuntimeException("The PHP extension apcu must be enabled with cli support (apc.enable_cli=1) in order to use this adapter.");
        }
    }

    /**
     * @inheritdoc
     */
    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
    }

    /**
     * @inheritdoc
     */
    public function get($key, $default = null)
    {
        $value = \apcu_fetch($this->getNamespacedKey($key));
        if ($value === false) {
            return $default;
        }
        return $value;
    }

    /**
     * @inheritdoc
     */
    public function set($key, $value, $ttl = null)
    {
        if (\is_null($ttl) || $ttl === WMAdapterCacheInterface::NEVER_EXPIRES) {
            $ttl = WMAdapterCacheInterface::NEVER_EXPIRES;
        }

        if ($ttl instanceof \DateInterval) {
            $ttl = (int)$ttl->format('%s');
        }

        return \apcu_store($this->getNamespacedKey($key), $value, $ttl);
    }

    /**
     * @inheritdoc
     */
    public function delete($key)
    {
        return \apcu_delete($this->getNamespacedKey($key));
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
        return \apcu_exists($this->getNamespacedKey($key));
    }

    /**
     * @inheritdoc
     */
    public function clear()
    {
        $namespace = $this->namespace;
        $it = new \APCUIterator();
        $removedItems = 0;
        foreach ($it as $key => $value) {
            if (strpos($key, $namespace) !== 0) {
                continue;
            }
            \apcu_delete($key);
            $removedItems++;
        }
        return $removedItems;
    }

    /**
     * Returns the namespaced key
     * @param $key
     * @return string
     */
    private function getNamespacedKey($key)
    {
        return $this->namespace . $key;
    }
}
