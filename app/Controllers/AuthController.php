<?php

namespace App\Controllers;

use App\Response\JsonResponse;
use Core\Auth\Auth;
use Core\Routing\Controller;
use Core\Http\Request;
use Core\Http\Respond;
use Core\Support\Time;
use Core\Valid\Validator;
use Firebase\JWT\JWT;

class AuthController extends Controller
{
    public function login(Request $request, JsonResponse $json): JsonResponse
    {
        $valid = Validator::make($request->only(['email', 'password']), [
            'email' => ['required', 'str', 'trim', 'min:5', 'max:30'],
            'password' => ['required', 'str', 'trim', 'min:8', 'max:20']
        ]);

        if ($valid->fails()) {
            return $json->errorBadRequest($valid->messages());
        }

        if (!Auth::attempt($valid->only(['email', 'password']))) {
            return $json->error([respond()->codeHttpMessage(Respond::HTTP_UNAUTHORIZED)], Respond::HTTP_UNAUTHORIZED);
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
