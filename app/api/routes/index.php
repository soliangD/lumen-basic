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

use Common\Services\User\LoginService;
use Laravel\Lumen\Routing\Router;

/** @var Router $router */
$router->group([
    'prefix' => 'api',
    'middleware' => ['setGuard:' . LoginService::GUARD_API],
], function (Router $router) {
    /********************* user *********************/
    // login
    require 'user/login.php';
    // user
    require 'user/user.php';
    /********************* other *********************/
});
