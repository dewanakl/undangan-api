<?php

namespace App\Middleware;

use Closure;
use Core\Auth\Auth;
use Core\Http\Request;
use Core\Middleware\MiddlewareInterface;
use Core\Support\Env;
use Core\Support\Time;

final class TzMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, Closure $next)
    {
        // override default timezone from env.
        Env::set('TIMEZONE', Auth::user()->tz);
        Time::setTimezoneDefault();

        return $next($request);
    }
}
