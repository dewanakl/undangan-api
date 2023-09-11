<?php

namespace App\Error;

use Core\Support\Error as BaseError;
use Core\Http\Request;
use Throwable;

class Error extends BaseError
{
    /**
     * Tampilkan errornya.
     *
     * @param Request $request
     * @param Throwable $th
     * @return mixed
     */
    public function render(Request $request, Throwable $th): mixed
    {
        //

        return parent::render($request, $th);
    }
}
