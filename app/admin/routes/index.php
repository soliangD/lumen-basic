<?php
/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

use Laravel\Lumen\Routing\Router;

/** @var Router $router */
$router->group([
    'prefix' => 'admin',
    'middleware' => ['cors', 'setGuard:admin'],
], function (Router $router) {

    require "test/test.php";
    /** 上传 */
    require 'upload/upload.php';
});
