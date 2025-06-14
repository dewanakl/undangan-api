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
            /**
             * Jika aplikasi tidak dalam mode debug, maka error tidak ditampilkan secara rinci.
             *
             * Anda dapat menggunakan `$id = request()->getRequestId();` untuk menelusuri error lebih lanjut.
             * Contoh penggunaannya:
             * - Cek log berdasarkan ID tersebut, misalnya di folder: cache/log/kamu.log
             */
            return $res->errorServer();
        }

        return $res->error([$this->getThrowable()->getMessage()], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
    }
}
