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
        if (https()) {
            $header->set('Access-Control-Allow-Credentials', 'true');
        }

        if (!$request->method(Request::OPTIONS)) {
            return $next($request);
        }

        if ($request->server->has('HTTP_ACCESS_CONTROL_REQUEST_METHOD')) {
            $header->set('Vary', 'Origin, User-Agent, Access-Control-Request-Method, Access-Control-Request-Headers')
                ->set(
                    'Access-Control-Allow-Methods',
                    strtoupper($request->server->get('HTTP_ACCESS_CONTROL_REQUEST_METHOD', $request->method()))
                )
                ->set(
                    'Access-Control-Allow-Headers',
                    $request->server->get('HTTP_ACCESS_CONTROL_REQUEST_HEADERS', 'Origin, Content-Type, Accept, Authorization, Token')
                );
        }

        return respond()->setCode(204);
    }
}
