<?php

namespace App\Error;

use Core\Support\Error as BaseError;
use Throwable;

class Error extends BaseError
{
    /**
     * Tampilkan errornya.
     *
     * @param Throwable $th
     * @return mixed
     */
    public function render(Throwable $th): mixed
    {
        //

        return parent::render($th);
    }
}
