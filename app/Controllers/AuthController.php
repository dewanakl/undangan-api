<?php

namespace App\Controllers;

use App\Response\JsonResponse;
use Core\Auth\Auth;
use Core\Routing\Controller;
use Core\Http\Request;
use Core\Valid\Validator;
use Firebase\JWT\JWT;

class AuthController extends Controller
{
    public function login(Request $request, JsonResponse $json)
    {
        $valid = Validator::make($request->only(['email', 'password']), [
            'email' => ['required', 'str', 'trim', 'min:5', 'max:30'],
            'password' => ['required', 'str', 'trim', 'min:8', 'max:20']
        ]);

        if ($valid->fails()) {
            return $json->error($valid->messages(), 400);
        }

        if (!Auth::attempt($valid->only(['email', 'password']))) {
            return $json->error(['unauthorized'], 401);
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

        return $json->success([
            'token' => $token,
            'user' => Auth::user()
        ], 200);
    }
}
