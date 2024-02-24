<?php

namespace App\Middleware;

use App\Response\JsonResponse;
use Closure;
use Core\Auth\Auth;
use Core\Http\Request;
use Core\Middleware\MiddlewareInterface;

final class DashboardMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::user()->is_admin) {
            return (new JsonResponse())->errorBadRequest(['role does not exist']);
        }

        return $next($request);
    }
}
