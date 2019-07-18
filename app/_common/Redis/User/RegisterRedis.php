<?php

namespace Common\Redis\User;

use Common\Helpers\Utils\Env;
use Common\Redis\BaseRedis;
use Common\Redis\RedisKey;

class RegisterRedis
{
    use BaseRedis;

    /**
     * 根据 code 获取用户id
     * @param $code
     * @return mixed
     */
    public function getByCode($code)
    {
        return $this->redis::get($this->getKey($code));
    }

    /**
     * 生成 注册 code
     * @param $userId
     * @param int $second 过期时间，默认 24h
     * @return mixed
     */
    public function generateCode($userId, $second = 86400)
    {
        $code = $this->redis::get($this->getUserKey($userId));

        if (!$code) {
            $code = $this->_generate($userId);
        }
        $this->saveCode($userId, $code, $second);

        return $code;
    }

    public function sendCount($email)
    {
        $key = $this->getSendCountKey($email, date('Y-m-d'));
        $res = $this->redis::incr($key);
        $this->redis::expire($key, 86400);

        return $res;
    }

    public function saveCode($userId, $code, $second)
    {
        $this->redis::set($this->getKey($code), $userId, 'EX', $second);
        $this->redis::set($this->getUserKey($userId), $code, 'EX', $second);
    }

    private function _generate($userId)
    {
        return strtoupper(md5(uniqid(Env::env() . '-' . $userId)));
    }

    protected function getKey($key)
    {
        return RedisKey::API_USER_REGISTER . $key;
    }

    protected function getUserKey($key)
    {
        return RedisKey::API_USER_REGISTER . 'user:' . $key;
    }

    protected function getSendCountKey($key, $prefix = '')
    {
        $prefix = $prefix ? $prefix . ':' : '';
        return RedisKey::API_USER_REGISTER . 'send_count:' . $prefix . $key;
    }
}
