<?php

namespace App\Middleware;

use Closure;
use Core\Http\Request;
use Core\Http\Respond;
use Core\Middleware\MiddlewareInterface;

final class CorsMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, Closure $next)
    {
        $header = respond()->getHeader();
        $header->set('Access-Control-Allow-Origin', '*');
        $header->set('Access-Control-Expose-Headers', 'Authorization, Content-Type, Cache-Control, Content-Disposition');

        $vary = $header->has('Vary') ? explode(', ', $header->get('Vary')) : [];
        $vary = array_unique([...$vary, 'Accept', 'Origin', 'User-Agent', 'Access-Control-Request-Method', 'Access-Control-Request-Headers']);
        $header->set('Vary', join(', ', $vary));

        if (!$request->method(Request::OPTIONS)) {
            return $next($request);
        }

        $header->unset('Content-Type');

        if (!$request->server->has('HTTP_ACCESS_CONTROL_REQUEST_METHOD')) {
            return respond()->setCode(Respond::HTTP_NO_CONTENT);
        }

        $header->set(
            'Access-Control-Allow-Methods',
            strtoupper($request->server->get('HTTP_ACCESS_CONTROL_REQUEST_METHOD', $request->method()))
        );

        $header->set(
            'Access-Control-Allow-Headers',
            $request->server->get('HTTP_ACCESS_CONTROL_REQUEST_HEADERS', 'Origin, Content-Type, Accept, Authorization, Accept-Language')
        );

        return respond()->setCode(Respond::HTTP_NO_CONTENT);
    }
}
