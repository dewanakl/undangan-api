<?php

namespace App\Response;

use Stringable;

class JsonResponse implements Stringable
{
    private $content;

    public function __toString(): string
    {
        return $this->content;
    }

    public function success(array|object $data, int $code): JsonResponse
    {
        $this->content = respond()->formatJson($data, null, $code);

        return $this;
    }

    public function error(array|object $error, int $code): JsonResponse
    {
        $this->content = respond()->formatJson(null, $error, $code);

        return $this;
    }
}
