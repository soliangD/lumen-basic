<?php

namespace Api\Services\User;

use Common\Events\User\RegisterEvent;
use Common\Helpers\Utils\UrlHelper;
use Common\Models\User\User;
use Common\Models\User\UserExt;
use Common\Redis\User\RegisterRedis;

class LoginService extends \Common\Services\User\LoginService
{
    /** @var int 注册code过期时间 24h */
    const REGISTER_CODE_EXPIRY = 86400;
    /** @var int 传参字段名 */
    const REGISTER_CODE_FIELD = 'code';

    public $hashRounds = 12;

    public $defaultGuard = self::GUARD_API;

    /**
     * 用户注册
     * @param $params
     * @return LoginService
     * @throws \Common\Exceptions\Exception\ApiException
     */
    public function register($params)
    {
        /** @var User $model */
        $model = User::getByEmail($params['email'], false);

        if ($model) {
            if ($model->isValid()) {
                $this->outputException('账号已存在');
            }
            if ($model->isNotActivated()) {
                // 单触发 注册->邮件发送事件
                event(new RegisterEvent($model->id, true));
                $this->outputException('账号激活中，请到邮箱确认激活');
            }
            return $this->outputError('注册失败(未知错误)，请联系管理员');
        }

        $params['password_hash'] = $this->encryptPassword($params['password']);
        $model = User::add($params, false);

        if (!$this->userCanSave($model) || !$model->save()) {
            return $this->outputError($this->getMsg() ?? '注册失败');
        }

        $token = $this->makeToken($model);

        $this->setAuthorizationHeader($token);

        // 触发注册事件
        event(new RegisterEvent($model->id));

        return $this->outputSuccess('注册成功', [
            'token' => $token,
        ]);
    }

    /**
     * 确认注册
     * @param $code
     * @return LoginService
     */
    public function confirmRegister($code)
    {
        $userId = RegisterRedis::redis()->getByCode($code);

        if (!$userId) {
            return $this->outputError('注册码无效或已过期，请重新验证');
        }

        $user = User::getBy($userId, false);

        if (!$user->isNotActivated()) {
            return $this->outputSuccess();
        }

        $user->changeStatusToNormal();

        UserExt::updOrAdd($user->id, UserExt::KEY_REGISTER_CONFIRM_TIME, date('Y-m-d H:i:s'));

        return $this->outputSuccess();
    }

    /**
     * 登录
     * @param $params
     * @return LoginService
     */
    public function login($params)
    {
        /** @var User $user */
        $user = User::getByEmail($params['account'], false);

        if (!$user || !$this->checkPassword($params['password'], $user->password_hash)) {
            return $this->outputError('账号名或密码错误');
        }

        if (!$this->userCanLogin($user)) {
            return $this->outputError($this->getMsg());
        }

        $token = $this->makeToken($user);

        $this->setAuthorizationHeader($token);

        return $this->outputSuccess('登录成功', [
            'token' => $token,
        ]);
    }

    /**
     * 判断用户能否登录
     * @param User $user
     * @return bool
     */
    public function userCanLogin(User $user)
    {
        if (!$user->isNormal()) {
            if ($user->isNotActivated()) {
                return $this->returnFalse('账号未激活，请检查邮箱收件');
            }
            return $this->returnFalse('账号状态不正确，请及时联系管理员');
        }
        return true;
    }

    /**
     * 判断 User 能否进行 save 操作
     * @param User $user
     * @return bool
     */
    public function userCanSave(User $user)
    {
        $usernameExist = User::getBy($user->username, false, 'username');

        if ($usernameExist) {
            return $this->returnFalse('用户名已存在');
        }

        return true;
    }

    /**
     * 生成注册链接
     * @param User $user
     * @return mixed
     */
    public function generateRegisterUrl(User $user)
    {
        $code = RegisterRedis::redis()->generateCode($user->id, self::REGISTER_CODE_EXPIRY);

        return UrlHelper::getFullUrl(config('app.url'), [self::REGISTER_CODE_FIELD => $code], '/api/api/user/login/register-confirm');
    }
}
