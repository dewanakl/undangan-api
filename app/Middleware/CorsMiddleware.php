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
        $header->set('Access-Control-Expose-Headers', 'Content-Length, Content-Disposition');

        $vary = $header->has('Vary') ? explode(', ', $header->get('Vary')) : [];
        $vary = array_unique([...$vary, 'Accept', 'Access-Control-Request-Method', 'Access-Control-Request-Headers', 'Origin']);
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

        $header->set('Access-Control-Allow-Headers', 'Accept, Authorization, Content-Type, x-access-key');

        return respond()->setCode(Respond::HTTP_NO_CONTENT);
    }
}
