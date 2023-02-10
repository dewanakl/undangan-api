<?php

namespace App\Controllers;

use Core\Auth\Auth;
use Core\Routing\Controller;
use Core\Http\Request;
use Firebase\JWT\JWT;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        if (!Auth::attempt($request->only(['email', 'password']))) {
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
                    'exp' => time() + (60 * 30)
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
