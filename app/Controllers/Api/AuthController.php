<?php

namespace App\Controllers\Api;

use App\Request\AuthRequest;
use App\Response\JsonResponse;
use Core\Auth\Auth;
use Core\Routing\Controller;
use Core\Http\Respond;
use Core\Support\Time;
use Exception;
use Firebase\JWT\JWT;
use Throwable;

class AuthController extends Controller
{
    public function login(AuthRequest $request, JsonResponse $json): JsonResponse
    {
        $valid = $request->validated();

        if ($valid->fails()) {
            return $json->errorBadRequest($valid->messages());
        }

        try {
            if (!Auth::attempt($valid->only(['email', 'password']))) {
                throw new Exception('Invalid credentials');
            }
        } catch (Throwable) {
            return $json->error(Respond::HTTP_UNAUTHORIZED);
        }

        if (!auth()->user()->isActive()) {
            return $json->errorBadRequest(['user not active.']);
        }

        $time = Time::factory()->getTimestamp();
        $token = JWT::encode(
            [
                'iat' => $time,
                'exp' => $time + (60 * 60),
                'iss' => base_url(),
                'sub' => strval(auth()->id()),
            ],
            env('JWT_KEY'),
            env('JWT_ALGO', 'HS256')
        );

        return $json->successOK([
            'token' => $token,
            'user' => Auth::user()->only(['name', 'email'])
        ]);
    }
}
