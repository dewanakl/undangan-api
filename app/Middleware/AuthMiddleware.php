<?php

namespace App\Middleware;

use App\Models\User;
use App\Response\JsonResponse;
use Closure;
use Core\Auth\Auth;
use Core\Http\Request;
use Core\Http\Respond;
use Core\Middleware\MiddlewareInterface;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

final class AuthMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, Closure $next)
    {
        try {
            if (!env('JWT_KEY')) {
                throw new Exception('JWT Key tidak ada !.');
            }

            $token = $request->bearerToken();

            if (!$token) {
                throw new Exception('Bearer token kosong!.');
            }

            Auth::login(new User((array) JWT::decode(
                $token,
                new Key(env('JWT_KEY'), env('JWT_ALGO', 'HS256'))
            )));
        } catch (Exception $e) {
            return (new JsonResponse)->errorBadRequest([$e->getMessage()]);
        }

        return $next($request);
    }
}
