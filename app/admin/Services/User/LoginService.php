<?php

namespace Admin\Services\User;

use Common\Models\User\User;
use Illuminate\Support\Facades\Auth;

class LoginService extends \Common\Services\User\LoginService
{
    public $hashRounds = 13;

    public $defaultGuard = self::GUARD_ADMIN;

    /**
     * 登录 待完善
     * @param $params
     * @return LoginService
     */
    public function login($params)
    {
        $this->setLoginExp();
        /** @var User $user */
        $user = User::getByEmail($params['account'], false);

        if (!$user || !$token = Auth::guard('admin')->attempt(['username' => 'xx', 'password' => 'xx'])) {
            return $this->outputError('账号名或密码错误');
        }

        if (!$user->isNormal()) {
        }

//        if (!$this->userCanLogin($user)) {
//            $this->outputError($this->getMsg());
//        }

//        $token = $this->makeToken($user);

        $this->setAuthorizationHeader($token);

        return $this->outputSuccess('登录成功', [
            'token' => $token,
        ]);
    }

    public function setLoginExp()
    {
        //$expireMinute = Config::model()->getLoginExpireHours() * 60; // 获取配置时间
        $expireMinute = '60';
        app('tymon.jwt.claim.factory')->setTTL($expireMinute); // 单位分钟

        $expireSec = $expireMinute * 60;

        return $expireSec;
    }

    /**
     * 保存ticket到redis
     * @param $adminId
     * @param $token
     */
    public function setRedis($adminId, $token)
    {
        $expireSec = $this->setLoginExp();
        //StaffIdRedis::redis()->set($adminId, $token, $expireTime);
    }
}
