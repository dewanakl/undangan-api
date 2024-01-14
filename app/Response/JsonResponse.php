<?php

namespace App\Response;

use Core\Http\Respond;

class JsonResponse extends Respond
{
    public function success(array|object|int $data, int|null $code = null): JsonResponse
    {
        if (is_int($data)) {
            $code = $data;
            $data = [$this->codeHttpMessage($code)];
        }

        $this->setContent(json([
            'code' => $code,
            'data' => $data,
            'error' => null
        ]));

        $this->headers->set('Content-Type', 'application/json');
        $this->setCode($code);

        return $this;
    }

    public function error(array|object|int $error, int|null $code = null): JsonResponse
    {
        if (is_int($error)) {
            $code = $error;
            $error = [$this->codeHttpMessage($code)];
        }

        $this->setContent(json([
            'code' => $code,
            'data' => null,
            'error' => $error
        ]));

        $this->headers->set('Content-Type', 'application/json');
        $this->setCode($code);

        return $this;
    }

    public function successOK(array|object $data): JsonResponse
    {
        return $this->success($data, Respond::HTTP_OK);
    }

    public function successStatusTrue(): JsonResponse
    {
        return $this->successOK(['status' => true]);
    }

    public function errorBadRequest(array|object $error): JsonResponse
    {
        return $this->error($error, Respond::HTTP_BAD_REQUEST);
    }

    public function errorNotFound(): JsonResponse
    {
        return $this->error(Respond::HTTP_NOT_FOUND);
    }

    public function errorServer(): JsonResponse
    {
        return $this->error(Respond::HTTP_INTERNAL_SERVER_ERROR);
    }
}
