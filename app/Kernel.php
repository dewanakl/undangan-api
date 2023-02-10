<?php

namespace App;

final class Kernel
{
    /**
     * Lokasi dari aplikasi ini.
     * 
     * @var string $path
     */
    private $path;

    /**
     * Init object
     * 
     * @return void
     */
    function __construct()
    {
        $this->path = dirname(__DIR__);
    }

    /**
     * Dapatkan lokasi dari app.
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Registrasi service agar bisa dijalankan.
     *
     * @return array
     */
    public function services(): array
    {
        return [
            \App\Providers\AppServiceProvider::class,
            \App\Providers\RouteServiceProvider::class,
        ];
    }

    /**
     * Kumpulan middleware yang dijalankan lebih awal.
     *
     * @return array
     */
    public function middlewares(): array
    {
        return [
            \App\Middleware\CorsMiddleware::class,
            \App\Middleware\XSSMiddleware::class,
            //\App\Middleware\CsrfMiddleware::class
        ];
    }
}
