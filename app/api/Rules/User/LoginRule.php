<?php

namespace Api\Rules\User;

use Common\Rule\Rule;

class LoginRule extends Rule
{
    /** @var string 注册 */
    const SCENARIO_REGISTER = 'register';
    /** @var string 登录 */
    const SCENARIO_LOGIN = 'login';

    /**
     * @return array|mixed
     */
    public function rules()
    {
        return [
            self::SCENARIO_REGISTER => [
                'email' => 'required|email',
                'username' => 'required|string',
                'password' => 'required',
                'confirm_password' => 'required|same:password',
            ],
            self::SCENARIO_LOGIN => [
                'account' => 'required|string',
                'password' => 'required|string',
            ]
        ];
    }

    /**
     * @return array|mixed
     */
    public function messages()
    {
        return [
            self::SCENARIO_REGISTER => [
                'confirm_password.same' => '两次密码输入不一致',
                'email.email' => '邮箱格式不正确',
                'email.unique' => '账号已存在',
            ]
        ];
    }

    /**
     * @return array
     */
    public function attributes()
    {
        return [
            'email' => '邮箱',
            'username' => '用户名',
            'password' => '密码',
            'confirm_password' => '确认密码',
            'account' => '邮箱',
        ];
    }
}
