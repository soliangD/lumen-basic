<?php

use Admin\Controllers\Test\UploadController;
use Laravel\Lumen\Routing\Router;

/**
 * @var Router $router
 */
$router->group([
    'prefix' => 'upload/upload',
], function (Router $router) {
    // 文件上传 /admin/upload/upload/create
    $router->post('/create', UploadController::class . '@create');
    // 文件列表 /admin/upload/upload/list
    $router->get('/list', UploadController::class . '@list');
    // 文件列表 /admin/upload/upload/delete
    $router->post('/delete', UploadController::class . '@delete');
});

