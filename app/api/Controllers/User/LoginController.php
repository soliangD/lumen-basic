<?php

namespace Api\Controllers\User;

use Api\Controllers\BaseController;
use Api\Rules\User\LoginRule;
use Api\Services\User\LoginService;

class LoginController extends BaseController
{
    /**
     * 注册
     * @param LoginRule $rule
     * @return array
     * @throws \Common\Exceptions\Exception\ApiException
     * @throws \Common\Exceptions\Exception\RuleException
     */
    public function register(LoginRule $rule)
    {
        $params = $rule->validateE($rule::SCENARIO_REGISTER);

        $service = LoginService::server()->register($params);

        return $this->resultJudge(
            $service->isSuccess(),
            $service->getMsg(),
            $service->getMsg(),
            $service->getData()
        );
    }

    /**
     * 确认注册
     * @return \Illuminate\Http\RedirectResponse|\Laravel\Lumen\Http\Redirector
     */
    public function confirmRegister()
    {
        $code = $this->getParam(LoginService::REGISTER_CODE_FIELD);

        $service = LoginService::server()->confirmRegister($code);

        $redirectUrl = env('WEB_URL', '');

        // TODO 根据结果重定向前端页面
        if (!$service->isSuccess()) {
            return redirect($redirectUrl);
        }

        return redirect($redirectUrl);
    }

    /**
     * 登录
     * @param LoginRule $rule
     * @return array
     * @throws \Common\Exceptions\Exception\RuleException
     */
    public function login(LoginRule $rule)
    {
        $params = $rule->validateE($rule::SCENARIO_LOGIN);

        $service = LoginService::server()->login($params);

        return $this->resultJudge(
            $service->isSuccess(),
            $service->getMsg(),
            $service->getMsg(),
            $service->getData()
        );
    }

    /**
     * 退出登录
     * @return array
     */
    public function logout()
    {
        LoginService::server()->logout();

        return $this->resultSuccess();
    }
}
