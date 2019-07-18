<?php

namespace Common\Redis\Lock;

use Common\Redis\BaseRedis;
use Common\Redis\RedisKey;

class LockRedis
{
    use BaseRedis;

    public function set($key, $second = 10, $value = 'lock')
    {
        return $this->redis::set($this->getKey($key), $value, 'EX', $second);
    }

    public function getKey($key)
    {
        return RedisKey::API_LOCK . $key;
    }

    public function get($key)
    {
        return $this->redis::get($this->getKey($key));
    }

    public function del($key)
    {
        return $this->redis::del($this->getKey($key));
    }
}
