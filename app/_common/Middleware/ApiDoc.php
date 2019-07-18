<?php

namespace Common\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiDoc
{
    public function handle(Request $request, Closure $next)
    {
        $check = $request->get('check');

        if ($check != '123456') {
            return response('check not provided');
        }

        return $next($request);
    }
}
