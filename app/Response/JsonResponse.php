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
}
