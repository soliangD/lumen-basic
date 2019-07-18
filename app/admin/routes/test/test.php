<?php

use Admin\Controllers\Test\TestController;
use Laravel\Lumen\Routing\Router;

/**
 * @var Router $router
 */
$router->group([
], function (Router $router) {
    // 测试
    $router->get('/test/demo', TestController::class . '@demo');
});

