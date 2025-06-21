<?php

namespace App\Middleware;

use App\Response\JsonResponse;
use Closure;
use Core\Http\Request;
use Core\Middleware\MiddlewareInterface;
use Core\Valid\Validator;

final class IpUserAgentMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, Closure $next)
    {
        $valid = Validator::make([
            'ip' => env('HTTP_CF_CONNECTING_IP') ? $request->server->get('HTTP_CF_CONNECTING_IP', $request->ip()) : $request->ip(),
            'user_agent' => $request->userAgent(),
        ], [
            'ip' => ['required', 'str', 'trim', 'min:2', 'max:45', 'ip'],
            'user_agent' => ['required', 'str', 'trim', 'min:64', 'max:512'],
        ]);

        if ($valid->fails()) {
            return (new JsonResponse)->errorBadRequest($valid->messages());
        }

        context('ip', $valid->ip);
        context('user_agent', $valid->user_agent);

        return $next($request);
    }
}
