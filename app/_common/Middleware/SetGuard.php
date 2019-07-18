<?php

namespace Common\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SetGuard
{
    public function handle(Request $request, Closure $next, $guard)
    {
        if ($guard) {
            Auth::setDefaultDriver($guard);
        }
        return $next($request);
    }
}
