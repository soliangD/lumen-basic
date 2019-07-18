<?php

namespace Common\Middleware;

use Closure;
use Common\Helpers\Utils\Env;
use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Support\Facades\Auth as AuthFacades;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class Authenticate
{
    /**
     * The authentication guard factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param \Illuminate\Contracts\Auth\Factory $auth
     * @return void
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param string|null $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        AuthFacades::setDefaultDriver($guard);

        $this->fakeUser();

        if ($this->auth->guard($guard)->guest()) {
            /** 清理登录态缓存... */
            throw new UnauthorizedHttpException(401, "登录已失效,请重新登录");
        }

        return $next($request);
    }

    private function fakeUser()
    {
        if (Env::isDev()) {
//            $user = new Staff();
//            $user->guard([]);
//            $user->fill([
//                'id' => 1,
//                'username' => 'admin@jiumiaodai.com',
//                'nickname' => 'admin',
//                'last_login_time' => DateHelper::dateTime(),
//                'last_login_ip' => '27.38.32.44',
//                'role' => null,
//                'time' => 1543809779,
//                'company_id' => 1,
//                'code' => '200',
//                'sso_msg' => '成功',
//                'sso_login_url' => 'https://sso.jiumiaodai.com/sso/login',
//                'sso_logout_url' => 'https://sso.jiumiaodai.com/sso/logout',
//                'isLogin' => true,
//            ]);
//            AuthFacades::setUser($user);
        }
    }
}
