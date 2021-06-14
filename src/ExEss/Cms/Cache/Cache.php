<?php

namespace ExEss\Cms\Cache;

class Cache
{
    public const DEFAULT = 'caches.default'; // not cleanable through DWP
    public const CONFIG = 'caches.config';
    public const CONTROLLER_RESPONSE = 'caches.controller.response';
    public const COMPILER_PATHS = 'caches.compiler.paths';
    public const TRANSLATION = 'caches.translations';
    public const PARSED_QUERY_USER = 'caches.parsed_query.user.id';

    public const TTL_DEFAULT = self::TTL_ONE_WEEK; // 1 week
    public const TTL_ONE_WEEK = 60 * 60 * 24 * 7;
    public const TTL_ONE_DAY = 60 * 60 * 24;
    public const TTL_ONE_HOUR = 60 * 60;
    public const TTL_TILL_MIDNIGHT = 'ttl-till-midnight';

    public const CACHE_POOLS = [
        self::DEFAULT => self::TTL_DEFAULT,
        self::CONFIG => self::TTL_DEFAULT,
        self::CONTROLLER_RESPONSE => 60 * 60 * 12,
        self::COMPILER_PATHS => self::TTL_DEFAULT,
        self::TRANSLATION => 0,
        self::PARSED_QUERY_USER => self::TTL_DEFAULT,
    ];

    /**
     * @param string|int $ttl
     */
    public static function getTtl($ttl): int
    {
        if ($ttl === Cache::TTL_TILL_MIDNIGHT) {
            return \strtotime('tomorrow') - \time();
        }

        return $ttl;
    }
}
