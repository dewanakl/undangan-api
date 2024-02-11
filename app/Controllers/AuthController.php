<?php

namespace App\Controllers;

use App\Request\AuthRequest;
use App\Response\JsonResponse;
use Core\Auth\Auth;
use Core\Routing\Controller;
use Core\Http\Respond;
use Core\Support\Time;
use Firebase\JWT\JWT;

class AuthController extends Controller
{
    public function login(AuthRequest $request, JsonResponse $json): JsonResponse
    {
        $valid = $request->validated();

        if ($valid->fails()) {
            return $json->errorBadRequest($valid->messages());
        }

        if (!Auth::attempt($valid->only(['email', 'password']))) {
            return $json->error(Respond::HTTP_UNAUTHORIZED);
        }

        $time = Time::factory()->getTimestamp();
        $token = JWT::encode(
            [
                'iat' => $time,
                'exp' => $time + (60 * 60),
                'iss' => base_url(),
                ...Auth::user()->only(['id', 'nama', 'email'])->toArray()
            ],
            env('JWT_KEY'),
            env('JWT_ALGO', 'HS256')
        );

        return $json->successOK([
            'token' => $token,
            'user' => Auth::user()->only(['nama', 'email'])
        ]);
    }
}
