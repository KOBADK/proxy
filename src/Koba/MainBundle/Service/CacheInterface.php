<?php
/**
 * @file
 * Contains the interface for the KOBA cache.
 */

namespace Koba\MainBundle\Service;

interface CacheInterface
{
    /**
     * Get an entry from the cache.
     *
     * @param $key
     *   The key to get entry for.
     * @return mixed
     *   The entry.
     */
    public function get($key);

    /**
     * Set an entry in the cache.
     *
     * @param $key
     *   Cache key.
     * @param $value
     *   The value to set.
     * @param integer|null $expire
     *   Optional expire time in seconds.
     *
     * @return void
     */
    public function set($key, $value, $expire = null);
}
