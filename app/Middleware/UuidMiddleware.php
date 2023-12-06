<?php

namespace App\Middleware;

use App\Response\JsonResponse;
use Closure;
use Core\Http\Request;
use Core\Middleware\MiddlewareInterface;
use Core\Valid\Validator;

final class UuidMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, Closure $next)
    {
        $valid = Validator::make(
            [
                'id' => $request->route('id')
            ],
            [
                'id' => ['required', 'str', 'trim', 'uuid', 'max:37']
            ]
        );

        if ($valid->fails()) {
            return (new JsonResponse)->errorBadRequest($valid->messages());
        }

        $request->route('id', $valid->get('id'));

        return $next($request);
    }
}
