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

        $header = respond()->getHeader();
        $header->set('Access-Control-Allow-Origin', '*');
        $header->set('Vary', 'Accept, Accept-Encoding, Access-Control-Request-Method, Access-Control-Request-Headers, Origin, User-Agent');

        if (!$request->method(Request::OPTIONS)) {
            return $next($request);
        }

        $header->unset('Content-Type');

        if (!$request->server->has('HTTP_ACCESS_CONTROL_REQUEST_METHOD')) {
            return respond()->setCode(204);
        }

        $header->set(
            'Access-Control-Allow-Methods',
            strtoupper($request->server->get('HTTP_ACCESS_CONTROL_REQUEST_METHOD', $request->method()))
        );

        $header->set(
            'Access-Control-Allow-Headers',
            $request->server->get('HTTP_ACCESS_CONTROL_REQUEST_HEADERS', 'Accept, Authorization, Content-Type, Origin, Token, User-Agent')
        );

        return respond()->setCode(204);
    }
}
