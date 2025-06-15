<?php

namespace App\Middleware;

use App\Response\JsonResponse;
use Closure;
use Core\Auth\Auth;
use Core\Http\Request;
use Core\Middleware\MiddlewareInterface;
use Core\Support\Env;
use Core\Support\Time;
use DateTimeZone;

final class TzMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, Closure $next)
    {
        $tz = Auth::user()->getTimezone();
        if (!in_array($tz, DateTimeZone::listIdentifiers(), true)) {
            return (new JsonResponse)->errorBadRequest(['Invalid time zone']);
        }

        Env::set('TIMEZONE', $tz);
        Time::setTimezoneDefault();

        return $next($request);
    }
}
