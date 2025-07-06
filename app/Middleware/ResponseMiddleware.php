<?php

namespace App\Middleware;

use Closure;
use Core\Http\Request;
use Core\Middleware\MiddlewareInterface;

final class ResponseMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, Closure $next)
    {
        $headers = respond()->getHeader();
        $headers->set('X-Accel-Buffering', 'no');

        if (!https()) {
            return $next($request);
        }

        $headers
            ->set('Referrer-Policy', 'strict-origin-when-cross-origin')
            ->set('Content-Security-Policy', 'upgrade-insecure-requests')
            ->set('X-Content-Type-Options', 'nosniff')
            ->set('X-Frame-Options', 'SAMEORIGIN');

        return $next($request);
    }
}
