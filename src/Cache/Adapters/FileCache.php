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

class FileCache implements WMAdapterCacheInterface
{

    /**
     * The number of characters of the hashed key to use for filename spreading
     * Do not change this unless you know what you're doing
     * @var integer
     */
    const SPREAD_CHARS = 6;

    /**
     * The number of characters after which to create a new directory
     * Do not change this unless you know what you're doing
     * @var integer
     */
    const SPREAD_DIVISOR = 2;
    /**
     * @var null|string
     */
    private $cache_dir;

    /**
     * @var string
     */
    private $namespace = 'wm_file';

    /**
     * FileCache constructor
     * @param null|string $cache_dir
     */
    public function __construct($cache_dir = null)
    {
        if (\is_null($cache_dir)) {
            $cache_dir = $this->getSystemTempDir();
        }

        $this->makeDirectory($cache_dir);

        if (!\is_dir($cache_dir)) {
            throw  new \InvalidArgumentException("The cache directory $cache_dir is now writable");
        }

        $this->cache_dir = $cache_dir;
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
        $file = $this->getFilePathForKey($key);

        $raw_data = @file_get_contents($file);
        // File does not exist
        if ($raw_data === false) {
            return $default;
        }

        $data = @unserialize($raw_data);
        $raw_data = null;

        // File contents cannot be unserialized
        if ($data === false || !is_array($data) || !isset($data['ttl']) || !isset($data['value'])) {
            @unlink($file);
            return $default;
        }

        $value = $data['value'];

        if ($data['ttl'] !== WMAdapterCacheInterface::NEVER_EXPIRES && \time() >= $data['ttl']) {
            @unlink($file);
            return $default;
        }

        return $value;
    }

    /**
     * @inheritdoc
     */
    public function set($key, $value, $ttl = null)
    {
        $file = $this->getFilePathForKey($key);
        $this->makeDirectory(dirname($file));

        $data = [
            'ttl' => $this->expiresAfter($ttl),
            'value' => $value
        ];

        return (bool)@file_put_contents($file, serialize($data), LOCK_EX);
    }

    private function expiresAfter($ttl)
    {
        if (\is_null($ttl) || $ttl === WMAdapterCacheInterface::NEVER_EXPIRES) {
            return WMAdapterCacheInterface::NEVER_EXPIRES;
        }
        if (\is_int($ttl) && $ttl > 0) {
            return \time() + $ttl;
        }
        if ($ttl instanceof \DateInterval) {
            return (int)(new \DateTime())->add($ttl)->getTimestamp();
        }
        throw new \InvalidArgumentException('Time to live must be a integer, DateInterval or null');
    }

    /**
     * @inheritdoc
     */
    public function delete($key)
    {
        return \unlink($this->getFilePathForKey($key));
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
        return $this->get($key) ? true : false;
    }

    /**
     * @inheritdoc
     */
    public function clear()
    {
        $cache_dir = $this->cache_dir . DIRECTORY_SEPARATOR . $this->namespace;

        if (!is_dir($cache_dir)) {
            return 0;
        }

        $removedItems = 0;

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($cache_dir, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $name => $file) {
            if ($file->isDir()) {
                @rmdir($name);
            } else {
                @unlink($name);
                $removedItems++;
            }
        }

        return $removedItems;
    }

    /**
     * Get the complete path and filename of the requested key, including the cache_dir
     * @param string $key
     * @return string path and filename
     */
    private function getFilePathForKey($key)
    {
        $hash = \hash('md5', $key);
        return $this->cache_dir .
            DIRECTORY_SEPARATOR .
            $this->namespace .
            DIRECTORY_SEPARATOR .
            \chunk_split(
                \substr($hash, 0, self::SPREAD_CHARS),
                self::SPREAD_DIVISOR,
                DIRECTORY_SEPARATOR
            ) . $hash;
    }

    /**
     * Returns the System Temp directory
     * @throws \Exception if it cannot be determined
     * @return string
     */
    private function getSystemTempDir()
    {
        if (function_exists('sys_get_temp_dir')) {
            if ($dir = sys_get_temp_dir()) {
                return self::addSlash($dir);
            }
        }
        foreach (['TMP', 'TEMP', 'TMPDIR'] as $env_name) {
            if ($dir = getenv($env_name)) {
                return self::addSlash($dir);
            }
        }
        throw new \Exception("Unable to locate System Temp directory");
    }

    /**
     * Returns $path with one trailing directory separator
     * @param string $path
     * @return string
     */
    private static function addSlash($path)
    {
        return \rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    }

    /**
     * @param string $directory
     * @internal param $file
     */
    private function makeDirectory($directory)
    {
        // We need to see if the directory exists and create it if not, so we'll just attempt
        // to create the directory and assume it exists.
        $old_umask = @umask(0);
        @mkdir($directory, 0777, true);
        @umask($old_umask);
    }
}
