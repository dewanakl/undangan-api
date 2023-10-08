<?php

namespace App\Response;

use Stringable;

class JsonResponse implements Stringable
{
    private $code;
    private $data;
    private $error;
    private $response;

    public function __toString(): string
    {
        return $this->response;
    }

    private function transform(): JsonResponse
    {
        $this->response = json([
            'code' => $this->code,
            'data' => $this->data ?? [],
            'error' => $this->error ?? []
        ], $this->code);

        return $this;
    }

    public function success(array|object $data, int $code): JsonResponse
    {
        $this->code = $code;
        $this->data = $data;

        return $this->transform();
    }

    public function error(array|object $error, int $code): JsonResponse
    {
        $this->code = $code;
        $this->error = $error;

        return $this->transform();
    }
}
