<?php

namespace App\Providers;

use Core\Facades\Provider;
use Core\Routing\Route;
use Core\Routing\Router;

class RouteServiceProvider extends Provider
{
    /**
     * Registrasi apa aja disini
     *
     * @return void
     */
    public function registrasi()
    {
        //
    }

    /**
     * Jalankan sewaktu aplikasi dinyalakan
     *
     * @return void
     */
    public function booting()
    {
        $this->app->singleton(Router::class);
        Route::setRouteFromCacheIfExist();
    }
}
