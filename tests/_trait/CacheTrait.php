<?php

namespace Tests\_trait;

use Illuminate\Support\Facades\Redis;

trait CacheTrait
{
    /** @var string 缓存 key 前缀 */
    protected $testRedisKey = 'test:test_cache:';

    protected function setCache($key, $value, $ex = 3600)
    {
        $value = json_encode($value);
        $key = $this->getCacheKey($key);
        return Redis::set($key, $value, 'EX', $ex);
    }

    protected function getCacheKey($key)
    {
        $key = $this->testRedisKey . static::class . '_' . $key;

        return $key;
    }

    protected function getCache($key, $assertNull = true, $ex = 3600)
    {
        $redisKey = $this->getCacheKey($key);
        $data = Redis::get($redisKey);
        Redis::expire($redisKey, $ex);

        $assertNull && $this->assertNotNull($data, $key . '缓存为空');

        return json_decode($data, true);
    }
}
