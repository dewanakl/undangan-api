<?php

namespace App\Middleware;

use Closure;
use Core\Http\Request;
use Core\Middleware\MiddlewareInterface;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

final class AuthMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, Closure $next)
    {
        try {
            $token = trim(substr($request->server('HTTP_AUTHORIZATION', ''), 6));
            context()->user = JWT::decode($token, new Key(env('JWT_KEY'), 'HS256'));
        } catch (Exception $e) {
            return json([
                'code' => 400,
                'data' => [],
                'error' => [$e->getMessage()]
            ], 400);
        }

        return $next($request);
    }
}
