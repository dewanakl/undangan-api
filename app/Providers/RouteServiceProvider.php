<?php

namespace App\Providers;

use App\Middleware\CookieMiddleware;
use App\Middleware\CsrfMiddleware;
use Core\Facades\Provider;
use Core\Routing\Route;
use Core\Routing\Router;

class RouteServiceProvider extends Provider
{
    /**
     * Prefix api.
     *
     * @var string $API_PREFIX
     */
    public static $API_PREFIX = '/api';

    /**
     * Jalankan sewaktu aplikasi dinyalakan.
     *
     * @return void
     */
    public function booting()
    {
        $this->app->singleton(Router::class);
        if (!Route::setRouteFromCacheIfExist()) {
            Route::middleware(CsrfMiddleware::class)->group(function () {
                Route::setRouteFromFile();
            });

            Route::prefix(static::$API_PREFIX)->middleware(CookieMiddleware::class)->group(function () {
                require_once base_path('/routes/api.php');
            });
        }
    }
}
