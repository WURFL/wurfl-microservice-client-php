<?php

/*
 * Copyright (c) 2017 ScientiaMobile Inc.
 *
 * The application programming interfaces and associated software (the “Platform APIs”)
 * with which this notice is distributed are the copyrighted materials of
 * ScientiaMobile, Inc. (“ScientiaMobile”).
 *
 * In order to be licensed to use the Platform APIs, the person or entity
 * using such Platform APIs must presently be a fully-paid up subscriber of
 * one of ScientiaMobile’s WURFL Microservice products for the appropriate
 * Platform and have agreed to the license terms and conditions to which
 * the WURFL Microservice (and correspondingly these Platform APIs) are licensed.
 *
 * You are advised that any other use of the Platform APIs constitutes and
 * infringement of ScientiaMobile’s intellectual property rights in and to
 * the Platform APIs.
 */

namespace ScientiaMobile\WMClient\Cache;

use ScientiaMobile\WMClient\Cache\Adapters\WMAdapterCacheInterface;

class UserAgentCache extends AbstractCache
{
    /**
     * @var array
     */
    private $importantHeaders = [];

    /**
     * UserAgentCache constructor.
     * @param WMAdapterCacheInterface $cache
     * @param array $importantHeaders The important headers
     * @param null|int $ttl
     */
    public function __construct(WMAdapterCacheInterface $cache, array $importantHeaders, $ttl)
    {
        parent::__construct($cache, $ttl);
        $this->importantHeaders = $importantHeaders;
    }

    /**
     * Make a cache key based on important header values
     * @param array $headers
     * @return string
     */
    public function makeKeyFromHeaders(array $headers)
    {
        $key = '';
        foreach ($this->importantHeaders as $headerName) {
            if (!isset($headers[$headerName])) {
                continue;
            }

            $key .= $headers[$headerName];
        }

        return \md5($key);
    }

    /**
     * @inheritdoc
     */
    public static function getNamespace()
    {
        return 'wm_ua';
    }
}
