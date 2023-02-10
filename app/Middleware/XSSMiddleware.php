<?php

namespace App\Middleware;

use Closure;
use Core\Http\Request;
use Core\Middleware\MiddlewareInterface;

final class XSSMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, Closure $next)
    {
        header('X-Content-Type-Options: nosniff');
        header('X-XSS-Protection: 1; mode=block');
        header('X-Frame-Options: SAMEORIGIN');

        header('Referrer-Policy: strict-origin-when-cross-origin');
        header('Content-Security-Policy: upgrade-insecure-requests');

        return $next($request);
    }
}
