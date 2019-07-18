<?php

namespace Common\Middleware;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CorsMiddleware
{
    private static $headers = [
        'Access-Control-Allow-Methods' => 'GET, POST, OPTIONS',
        'Access-Control-Allow-Credentials' => 'true',//允许客户端发送cookie
        'Access-Control-Max-Age' => 1728000, //该字段可选，用来指定本次预检请求的有效期，在此期间，不用发出另一条预检请求。
    ];
    /**
     * @var bool
     */
    private static $load = false;

    private $allowOrigin;

    /**
     * @param Request $request
     * @param \Closure $next
     * @return Response|mixed
     */
    public function handle(Request $request, \Closure $next)
    {
        $response = $next($request);

        $this->getCorsHeaders($request);

        return $this->setCorsHeaders($response);
    }

    public function getCorsHeaders($request)
    {
        $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
        self::$headers['Access-Control-Allow-Headers'] = $request->header('Access-Control-Request-Headers');
        $allowOriginList = config('config.allow_origin', []);
        if (in_array($origin, $allowOriginList)) {
            $this->allowOrigin = $origin;
        }
        if (in_array('*', $allowOriginList)) {
            // 当设置允许cookie跨域时，Allow-Origin不能设置为 *
            if (array_get(self::$headers, 'Access-Control-Allow-Credentials') == true) {
                $this->allowOrigin = $origin;
            } else {
                $this->allowOrigin = '*';
            }
        }

        if ($this->allowOrigin) {
            self::$headers['Access-Control-Allow-Origin'] = $this->allowOrigin;
        } else {
            self::$headers = [];
        }

        return self::$headers;
    }

    /**
     * @param Response $response
     * @return mixed
     */
    public function setCorsHeaders($response)
    {
        //这个判断是因为在开启session全局中间件之后，频繁的报header方法不存在，所以加上这个判断，存在header方法时才进行header的设置
        if (!is_callable(array($response, 'header'), false)) {
            return $response;
        }
        if (!self::$load) {
            foreach (self::$headers as $key => $value) {
                if ($value) {
                    $response->header($key, $value);
                }
            }
            self::$load = true;
        }
        return $response;
    }
}
