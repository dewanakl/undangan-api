<?php

namespace App\Controllers;

use Core\Routing\Controller;
use Core\Http\Request;

class WelcomeController extends Controller
{
    public function __invoke(): \Core\View\View
    {
        return $this->view('welcome', [
            'data' => 'PHP Framework'
        ]);
    }

    public function debug()
    {
        if (request()->get('key') !== env('JWT_KEY')) {
            return;
        }

        dd(request());
    }
}
