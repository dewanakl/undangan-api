<?php

namespace App\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Core\Http\Request;
use Core\Middleware\MiddlewareInterface;
use Core\Support\Env;

final class CookieMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, Closure $next)
    {
        if (
            strpos($request->server->get('REQUEST_URL'), RouteServiceProvider::$API_PREFIX) !== false
            && $request->server->get('REQUEST_URL') !== RouteServiceProvider::$API_PREFIX
        ) {
            Env::set('COOKIE', 'false');
        }

        return $next($request);
    }
}
