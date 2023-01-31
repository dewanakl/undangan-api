<?php

namespace App\Middleware;

use Closure;
use Core\Http\Request;
use Core\Middleware\MiddlewareInterface;

final class CorsMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, Closure $next)
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
        header('Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization');

        header('Access-Control-Max-Age: 86400');
        header('Vary: origin');

        header('X-Content-Type-Options: nosniff');
        header('X-XSS-Protection: 1; mode=block');
        header('X-Frame-Options: SAMEORIGIN');

        if (https()) {
            header('Referrer-Policy: strict-origin-when-cross-origin');
            header('Content-Security-Policy: upgrade-insecure-requests');
        }

        return $next($request);
    }
}
