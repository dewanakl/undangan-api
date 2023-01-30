<?php

namespace App\Middleware;

use Closure;
use Core\Http\Request;
use Core\Middleware\HasToken;
use Core\Middleware\MiddlewareInterface;
use Core\Valid\Hash;

final class CsrfMiddleware implements MiddlewareInterface
{
    use HasToken;

    public function handle(Request $request, Closure $next)
    {
        if ($request->method() != 'GET' && (!$request->ajax())) {
            $this->checkToken($request->get('_token', Hash::rand(10)));
        }

        if ($request->ajax()) {
            $this->checkToken($request->ajax(), true);
        }

        return $next($request);
    }
}
