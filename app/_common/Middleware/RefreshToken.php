<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/21
 * Time: 14:19
 * author: soliang
 */

namespace Common\Middleware;

use Closure;
use Common\Services\User\LoginService;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class RefreshToken extends BaseMiddleware
{
    protected $plainArray;

    public function handle($request, Closure $next, $guard = null)
    {
        // 另建了 setGuard 来处理
        // Auth::setDefaultDriver($guard);

        // 检查此次请求中是否带有 token，如果没有则抛出异常。
        $this->checkForToken($request);

        // 使用 try 包裹，以捕捉 token 过期所抛出的 TokenExpiredException 异常
        try {
            $this->resolverPlain();

            // 判断模块是否正确，防止 token 套用
            // 检测用户的登录状态，如果正常则通过
            if ($this->checkSubject() && $this->auth->authenticate()) {
                return $next($request);
            }

            throw new UnauthorizedHttpException('jwt-auth', '未登录');
        } catch (TokenExpiredException $exception) {
            // 此处捕获到了 token 过期所抛出的 TokenExpiredException 异常，刷新该用户的 token 并将它添加到响应头中(实现自动刷新token)
            try {
                // 刷新用户的 token
                $token = Auth::refresh();

                $userId = $this->plainArray['sub'];

                // 判断用户能否进行登录操作(此操作必须在 refresh之后，保证之前token拉入黑名单)
                $this->checkCanLogin($userId);

                // 使用一次性登录以保证此次请求的成功
                Auth::onceUsingId($userId);
            } catch (JWTException $exception) {
                // 如果捕获到此异常，即代表 refresh 也过期了，用户无法刷新令牌，需要重新登录。
                throw new UnauthorizedHttpException('jwt-auth', $exception->getMessage());
            }
        } catch (JWTException $exception) {
            // 捕获其他jwt错误，如：token无效...
            throw new UnauthorizedHttpException('jwt-auth', $exception->getMessage());
        }

        // 在响应头中返回新的 token
        return $this->setAuthenticationHeader($next($request), $token);
    }

    /**
     * 解析 token
     * @throws JWTException
     */
    protected function resolverPlain()
    {
        $this->auth->parseToken();
        $this->plainArray = $this->auth->manager()->getJWTProvider()->decode($this->auth->getToken()->get());
        // 另一种方式解析 $this->auth->manager()->getPayloadFactory()->buildClaimsCollection()->toPlainArray()['sub']

        if (!isset($this->plainArray['sub']) || !isset($this->plainArray['prv'])) {
            throw new UnauthorizedHttpException('jwt-auth', 'token plain decode error');
        }
    }

    /**
     * hash subject
     * @param $model
     * @return string
     */
    protected function hashSubjectModel($model)
    {
        return sha1(is_object($model) ? get_class($model) : $model);
    }

    /**
     * 判断 subject 是否相等
     * @return bool
     */
    protected function checkSubject()
    {
        if (!isset($this->plainArray['prv'])) {
            return false;
        }
        $prv = $this->plainArray['prv'];
        return $this->hashSubjectModel(Auth::getProvider()->getModel()) === $prv;
    }

    /**
     * 判断能否登录
     * @param $id
     * @return bool
     */
    protected function checkCanLogin($id)
    {
        if (!LoginService::server()->canLogin($id)) {
            throw new UnauthorizedHttpException('jwt-auth', '未登录');
        }
    }
}
