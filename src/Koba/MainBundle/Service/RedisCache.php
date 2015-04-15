<?php

namespace Koba\MainBundle\Service;

/**
 * Class RedisCache
 * @package Koba\MainBundle\Service
 */
class RedisCache implements CacheInterface {
  protected $redis;

  /**
   * Constructor.
   *
   * @param $redis
   *   The redis service.
   */
  public function __construct($redis) {
    $this->redis = $redis;
  }

  /**
   * @inheritdoc
   */
  public function get($key) {
    return $this->redis->get($key);
  }

  /**
   * @inheritdoc
   */
  public function set($key, $value, $expire = NULL) {
    $this->redis->set($key, $value);
    if ($expire) {
      $this->redis->expire($key, $expire);
    }
  }
}