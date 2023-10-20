<?php

namespace App\Response;

use Core\Http\Respond;

class JsonResponse extends Respond
{
    public function success(array|object $data, int $code): JsonResponse
    {
        $this->setContent($this->formatJson($data, [], $code));

        return $this;
    }

    public function error(array|object $error, int $code): JsonResponse
    {
        $this->setContent($this->formatJson([], $error, $code));

        return $this;
    }
}
