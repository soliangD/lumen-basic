<?php

namespace Common\Exceptions;

use Common\Exceptions\Exception\ApiException;
use Common\Exceptions\Exception\RuleException;
use Common\Helpers\Utils\Env;
use Common\Middleware\CorsMiddleware;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * @param Exception $exception
     * @throws Exception
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param Exception $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        $responseErrorDetail = true;

        if ($e instanceof HttpResponseException) {
            //return $e->getResponse();
        } elseif ($e instanceof ModelNotFoundException) {
            $e = new NotFoundHttpException($e->getMessage(), $e);
        } elseif ($e instanceof AuthorizationException) {
            $e = new HttpException(403, $e->getMessage());
            //} elseif ($e instanceof ValidationException && $e->getResponse()) {
            //    return $e->getResponse();
        } elseif ($e instanceof RuleException || $e instanceof ApiException) {
            $responseErrorDetail = false;
        }

        $code = ($e instanceof HttpExceptionInterface) ? $e->getStatusCode() : 13000;
        $msg = $e->getMessage() ?: $code . ' ' . self::getStatusText($e->getStatusCode());
        $error = [];
        if ($responseErrorDetail && !Env::isProd()) {
            $error = [
                'file' => $e->getFile() . ':' . $e->getLine(),
                'class' => get_class($e),
                'trace' => explode("\n", $e->getTraceAsString()),
            ];
        }
        return response()->json($this->result(
            $code,
            $msg,
            $error
        ), 200, (new CorsMiddleware())->getCorsHeaders($request));
//        ), 200);
    }

    /**
     * 接口统一输出
     * @param int $code
     * @param string $msg
     * @param array $data
     * @return array
     */
    public function result($code = 18000, $msg = '', $data = [])
    {
        return [
            'code' => $code,
            'msg' => $msg,
            'data' => $data,
        ];
    }

    /**
     * @param $status
     * @return array|mixed|string
     */
    public static function getStatusText($status)
    {
        return Response::$statusTexts[$status] ?? 'unknown status';
    }
}
