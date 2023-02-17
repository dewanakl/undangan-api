<?php

namespace App\Controllers;

use Core\Auth\Auth;
use Core\Routing\Controller;
use Core\Http\Request;
use Core\Valid\Validator;
use Firebase\JWT\JWT;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $valid = Validator::make($request->only(['email', 'password']), [
            'email' => ['required', 'str', 'trim', 'min:5', 'max:30'],
            'password' => ['required', 'str', 'trim', 'min:8', 'max:20']
        ]);

        if ($valid->fails()) {
            return json([
                'code' => 400,
                'data' => [],
                'error' => $valid->messages()
            ], 400);
        }

        if (!Auth::attempt($valid->only(['email', 'password']))) {
            return json([
                'code' => 401,
                'data' => [],
                'error' => ['unauthorized']
            ], 401);
        }

        $token = JWT::encode(
            array_merge(
                [
                    'iat' => time(),
                    'exp' => time() + (60 * 60)
                ],
                Auth::user()->only(['id', 'nama'])->toArray()
            ),
            env('JWT_KEY'),
            'HS256'
        );

        return json([
            'code' => 200,
            'data' => [
                'token' => $token,
                'user' => Auth::user()
            ],
            'error' => []
        ]);
    }
}
