<?php

namespace App\Middleware;

use Closure;
use Core\Http\Request;
use Core\Http\Session;
use Core\Middleware\HasToken;
use Core\Middleware\MiddlewareInterface;
use Core\Valid\Hash;

final class CsrfMiddleware implements MiddlewareInterface
{
    use HasToken;

    public function handle(Request $request, Closure $next)
    {
        $result = null;

        if (!$request->method(Request::GET) && !$request->ajax()) {
            $result = $this->checkToken($request->get(Session::TOKEN, Hash::rand(10)));
        } else if ($request->ajax()) {
            $result = $this->checkToken($request->ajax(), true);
        }

        if ($result) {
            return $result;
        }

        return $next($request);
    }
}
