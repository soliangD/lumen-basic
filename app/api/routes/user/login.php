<?php

use Api\Controllers\User\LoginController;
use Laravel\Lumen\Routing\Router;

/**
 * @var Router $router
 */
$router->group([
    'prefix' => 'user/login',
], function (Router $router) {
    // 注册 /api/user/login/register
    $router->post('/register', LoginController::class . '@register');
    // 确认注册 /api/user/login/register-confirm
    $router->get('/register-confirm', LoginController::class . '@confirmRegister');
    // 登录 /api/user/login/login
    $router->post('/login', LoginController::class . '@login');
    // 退出登录 /api/user/login/logout
    $router->post('/logout', LoginController::class . '@logout');
});
