<?php

namespace App\Middleware;

use Closure;
use Core\Http\Request;
use Core\Middleware\MiddlewareInterface;

final class XSSMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, Closure $next)
    {
        if (!https()) {
            return $next($request);
        }

        respond()->getHeader()
            ->set('Referrer-Policy', 'strict-origin-when-cross-origin')
            ->set('Content-Security-Policy', 'upgrade-insecure-requests')
            ->set('X-Content-Type-Options', 'nosniff')
            ->set('X-XSS-Protection', '1; mode=block')
            ->set('X-Frame-Options', 'SAMEORIGIN');

        return $next($request);
    }
}
