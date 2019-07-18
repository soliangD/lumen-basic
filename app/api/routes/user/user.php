<?php

use Api\Controllers\User\UserController;
use Laravel\Lumen\Routing\Router;

/**
 * @var Router $router
 */
$router->group([
    'prefix' => 'user/user',
    'middleware' => ['refreshToken'],
], function (Router $router) {
    // 获取用户信息 /api/user/user/info
    $router->get('/info', UserController::class . '@info');
});
