<?php

namespace Tests\Api\User;

use Carbon\Carbon;
use Common\Events\User\RegisterEvent;
use Common\Helpers\Utils\DateHelper;
use Tests\Api\ApiTestBase;

class LoginTest extends ApiTestBase
{
    public function testRegister()
    {
        $params = [
            'email' => '937730775@qq.com',
            'username' => 'soliangZ',
            'password' => '123456',
            'confirm_password' => '123456',
        ];
        $res = $this->post('api/api/user/login/register', $params)
            ->assertSuccess()
            ->getData();

        $this->setCache('token', $res['token']);
    }

    public function testLogin()
    {
        $params = [
            'account' => '297210725@qq.com',
            'password' => '123456',
        ];

        $res = $this->post('api/api/user/login/login', $params)
            ->assertSuccess()
            ->getData();

        $this->setCache('token', $res['token']);
    }

    public function testUserInfo()
    {
        $params = [
            'token' => $this->getCache('token'),
        ];
        $this->get('api/api/user/user/info', $params)->getData();
    }

    /**
     * 退出登录
     */
    public function testLogout()
    {
        $params = [
            'token' => $this->getCache('token'),
        ];
        $this->post('api/api/user/login/logout', $params)->getData();
    }

    public function testT()
    {
        $res = event(new RegisterEvent(9));
        dd($res);
    }
}
