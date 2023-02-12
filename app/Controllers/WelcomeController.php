<?php

namespace App\Controllers;

use Core\Routing\Controller;
use Core\Http\Request;

class WelcomeController extends Controller
{
    public function __invoke()
    {
        return $this->view('welcome', [
            'data' => 'PHP Framework'
        ]);
    }
}
