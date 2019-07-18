<?php

namespace Common\Helpers\Utils;

use Common\Helpers\BaseHelpers;
use Common\Redis\Lock\LockRedis;

class LockHelper extends BaseHelpers
{
    // 测试锁
    const LOCK_TEST = 'test:';

    /**
     * 测试锁
     * @param $userId
     * @return bool
     */
    public function test($userId)
    {
        $key = self::LOCK_TEST . $userId;
        return $this->addLock($key);
    }

    /**
     * 公共添加锁
     * @param $key
     * @param int $second 秒
     * @return bool
     */
    public function addLock($key, $second = 10)
    {
        if (LockRedis::redis()->get($key)) {
            return false;
        }
        LockRedis::redis()->set($key, $second);
        return true;
    }

    /**
     * 判断是否有锁
     * @param $key
     * @return bool
     */
    public function hasLock($key)
    {
        if (!LockRedis::redis()->get($key)) {
            return false;
        }
        return true;
    }
}
