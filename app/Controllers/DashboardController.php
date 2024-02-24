<?php

namespace App\Controllers;

use Core\Routing\Controller;
use Core\Http\Request;

class DashboardController extends Controller
{
    public function __invoke()
    {
        return $this->view('dashboard');
    }
}
