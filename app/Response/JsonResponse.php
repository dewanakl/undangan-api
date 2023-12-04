<?php

namespace App\Response;

use Core\Http\Respond;

class JsonResponse extends Respond
{
    public function success(array|object $data, int $code): JsonResponse
    {
        $this->setContent(json([
            'code' => $code,
            'data' => $data,
            'error' => null
        ]));

        $this->headers->set('Content-Type', 'application/json');
        $this->setCode($code);

        return $this;
    }

    public function error(array|object $error, int $code): JsonResponse
    {
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

    public function errorBadRequest(array|object $error): JsonResponse
    {
        return $this->error($error, Respond::HTTP_BAD_REQUEST);
    }

    public function errorNotFound(): JsonResponse
    {
        return $this->error([$this->codeHttpMessage(Respond::HTTP_NOT_FOUND)], Respond::HTTP_NOT_FOUND);
    }

    public function errorServer(): JsonResponse
    {
        return $this->error([$this->codeHttpMessage(Respond::HTTP_INTERNAL_SERVER_ERROR)], Respond::HTTP_INTERNAL_SERVER_ERROR);
    }
}
