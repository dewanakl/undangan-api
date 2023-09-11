<?php

namespace App\Middleware;

use Closure;
use Core\Http\Request;
use Core\Middleware\MiddlewareInterface;

final class CorsMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->ajax() && !$request->method(Request::OPTIONS)) {
            return $next($request);
        }

        respond()->getHeader()->set('Access-Control-Allow-Origin', '*')
            ->set('Access-Control-Allow-Credentials', 'true')
            ->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
            ->set('Access-Control-Allow-Headers', 'Origin, Content-Type, Accept, Authorization, Token')
            ->set('Vary', 'Accept-Encoding, Origin, User-Agent');

        if (!$request->method(Request::OPTIONS)) {
            return $next($request);
        }

        return respond()->setCode(204);
    }
}
