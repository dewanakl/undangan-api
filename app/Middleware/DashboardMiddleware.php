<?php

namespace App\Middleware;

use Closure;
use Core\Auth\Auth;
use Core\Http\Exception\NotFoundException;
use Core\Http\Request;
use Core\Middleware\MiddlewareInterface;

final class DashboardMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::user()->isAdmin()) {
            return $next($request);
        }

        NotFoundException::json();
        throw new NotFoundException;
    }
}
