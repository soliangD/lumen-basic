<?php

require_once __DIR__ . '/../vendor/autoload.php';

(new Laravel\Lumen\Bootstrap\LoadEnvironmentVariables(
    dirname(__DIR__)
))->bootstrap();

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| Here we will load the environment and create the application instance
| that serves as the central piece of this framework. We'll use this
| application as an "IoC" container and router for this framework.
|
*/

$app = new Laravel\Lumen\Application(
    dirname(__DIR__)
);

$app->withFacades();

$app->withEloquent();

/*
|--------------------------------------------------------------------------
| Config
|--------------------------------------------------------------------------
*/
$app->configure('config');
// auth
$app->configure('auth');
// jwt
$app->configure('jwt');
// yaml-swagger
$app->configure('yaml-swagger');

/*
|--------------------------------------------------------------------------
| Register Container Bindings
|--------------------------------------------------------------------------
|
| Now we will register a few bindings in the service container. We will
| register the exception handler and the console kernel. You may add
| your own bindings here if you like or you can make another file.
|
*/

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    Common\Exceptions\Handler::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    Common\Console\Kernel::class
);

/*
|--------------------------------------------------------------------------
| Register Middleware
|--------------------------------------------------------------------------
|
| Next, we will register the middleware with the application. These can
| be global middleware that run before and after each request into a
| route or middleware that'll be assigned to some specific routes.
|
*/

//$app->middleware([
//    'cors' => \Common\Middleware\CorsMiddleware::class,
//]);

$app->routeMiddleware([
    //'auth' => App\Http\Middleware\Authenticate::class,
    'cors' => \Common\Middleware\CorsMiddleware::class,
    'setGuard' => \Common\Middleware\SetGuard::class,
    'refreshToken' => \Common\Middleware\RefreshToken::class,
]);

/*
|--------------------------------------------------------------------------
| Register Service Providers
|--------------------------------------------------------------------------
|
| Here we will register all of the application's service providers which
| are used to bind services into the container. Service providers are
| totally optional, so you are not required to uncomment this line.
|
*/

//redis
$app->register(Illuminate\Redis\RedisServiceProvider::class);
//AppServiceProvider
$app->register(\Common\Providers\AppServiceProvider::class);
// $app->register(\Common\Providers\AuthServiceProvider::class);
$app->register(\Common\Providers\EventServiceProvider::class);
//ide helper
$app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
// jwt 由于 jwt-auth 存在问题，使用重写后的 Provider
//$app->register(Tymon\JWTAuth\Providers\LumenServiceProvider::class);
$app->register(\Common\Providers\Rewrite\LumenServiceProvider::class);

/*
|--------------------------------------------------------------------------
| Run in the development environment
|--------------------------------------------------------------------------
*/
if (\Common\Helpers\Utils\Env::isDevOrTest()) {
    $app->routeMiddleware([
        'apiDoc' => \Common\Middleware\ApiDoc::class,
    ]);
    // swagger
    $app->register(\Soocoo\Swagger\SwaggerLumenServiceProvider::class);
}

/*
|--------------------------------------------------------------------------
| Load The Application Routes
|--------------------------------------------------------------------------
|
| Next we will include the routes file so that they can all be added to
| the application. This will provide all of the URLs the application
| can respond to, as well as the controllers that may handle them.
|
*/

$app->router->group([], function ($router) {
    /**
     * 保证路由格式一致化
     * 路由路径组成：①/②/③/④/⑤
     * ①：固定 api 前缀，用来配置 nginx 前缀重定向。在前端地址 nginx 处配置 api 转发到后端接口地址
     * ②：客户端区分。如：admin 为管理后台接口模块，api 为web端接口模块
     * ③：模块区分。一般根据 controller 目录区分。如：upload 上传模块对应 controller 目录中的 Upload
     * ④：控制器区分。一般具体到模块内的具体 controller 名
     * ④：方法标识。一般为 controller 内的具体方法
     * 例：/api/api/user/login/register
     * 表示 api 客户端| user 模块| login 控制器 | register 方法
     */
    $router->group([
        'prefix' => 'api',
    ], function ($router) {
        require __DIR__ . "/../app/admin/routes/index.php";
        require __DIR__ . "/../app/api/routes/index.php";
    });
    $router->get('/', function () use ($router) {
        return $router->app->version();
    });
    $router->get('/phpinfo', function () {
        phpinfo();
    });
});

return $app;
