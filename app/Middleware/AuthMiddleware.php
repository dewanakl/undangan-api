<?php

namespace App\Middleware;

use App\Models\User;
use App\Response\JsonResponse;
use Closure;
use Core\Auth\Auth;
use Core\Http\Request;
use Core\Middleware\MiddlewareInterface;
use Core\Valid\Validator;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

final class AuthMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->bearerToken()) {
            try {
                if (!env('JWT_KEY')) {
                    throw new Exception('JWT Key not found!.');
                }

                $token = JWT::decode(
                    $request->bearerToken(),
                    new Key(env('JWT_KEY'), env('JWT_ALGO', 'HS256'))
                );

                $user = User::find($token->id);
                if (!$user->exist()) {
                    throw new Exception('user not found');
                }

                if (!boolval($user->is_active)) {
                    throw new Exception('user not active');
                }

                $user->is_admin = true;

                Auth::login($user);
            } catch (Exception $e) {
                return (new JsonResponse)->errorBadRequest([$e->getMessage()]);
            }

            return $next($request);
        }

        $valid = Validator::make(
            [
                'key' => $request->server->get('HTTP_X_ACCESS_KEY')
            ],
            [
                'key' => ['required', 'str', 'trim', 'alpha_num', 'min:49', 'max:50']
            ]
        );

        if ($valid->fails()) {
            return (new JsonResponse)->errorBadRequest($valid->messages());
        }

        $user = User::where('access_key', $valid->key)->limit(1)->first();
        if (!$user->exist()) {
            return (new JsonResponse)->errorBadRequest(['user not found.']);
        }

        if (!boolval($user->is_active)) {
            return (new JsonResponse)->errorBadRequest(['user not active.']);
        }

        Auth::login($user);
        return $next($request);
    }
}
