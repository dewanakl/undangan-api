<?php

namespace App\Error;

use App\Response\JsonResponse;
use Core\Support\Error as BaseError;

class Error extends BaseError
{
    /**
     * Tampilkan errornya.
     *
     * @return mixed
     */
    public function render(): mixed
    {
        $res = new JsonResponse();
        $res->headers->set('Access-Control-Allow-Origin', '*');
        $res->headers->set('Access-Control-Allow-Methods', '*');

        if (!debug()) {
            return $res->errorServer();
        }

        return $res->error([$this->getThrowable()->getMessage()], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
    }
}
