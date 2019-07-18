<?php

namespace Common\Services\User;

use Common\Helpers\Utils\Env;
use Common\Models\User\User;
use Common\Services\BaseService;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginService extends BaseService
{
    /**
     * guard api
     */
    const GUARD_API = 'api';
    /**
     * guard admin
     */
    const GUARD_ADMIN = 'admin';

    public $hashRounds = 11;

    protected $defaultGuard = null;

    /**
     * password hash
     * @param $password
     * @return string
     */
    public function encryptPassword($password)
    {
        return Hash::make($password, [
            'rounds' => $this->hashRounds,
        ]);
    }

    /**
     * 密码验证
     * @param $password
     * @param $passwordHash
     * @return bool
     */
    public function checkPassword($password, $passwordHash)
    {
        return Hash::check($password, $passwordHash);
    }

    /**
     * 生成 token
     * @param Authenticatable $user
     * @return mixed
     */
    public function makeToken(Authenticatable $user)
    {
        $token = Auth::guard($this->defaultGuard)->login($user);

        return $token;
    }

    /**
     * 退出登录
     */
    public function logout()
    {
        Auth::guard($this->defaultGuard)->logout();
    }

    /**
     * token 添加至 header
     * @param $token
     */
    public function setAuthorizationHeader($token)
    {
        // 添加 header，测试用例排除 运行会报错
        if (!Env::isTesting()) {
            header('Authorization:Bearer ' . $token);
        }
    }

    /**
     * 判断能否进行登录
     * @param $id
     * @param $guard
     * @return bool
     */
    public function canLogin($id, $guard = null)
    {
        if (!$guard) {
            $guard = Auth::getDefaultDriver();
        }

        switch ($guard) {
            case self::GUARD_API:
                $user = User::getBy($id);
                return $user && \Api\Services\User\LoginService::server()->userCanLogin($user);
            case self::GUARD_ADMIN:
                // todo
                return true;
        }

        return false;
    }
}
