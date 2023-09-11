<?php

namespace App;

use Core\Kernel\KernelContract;

/**
 * Kernel dari applikasi ini, semua hal penting ada disini
 *
 * @class Kernel
 */
final class Kernel implements KernelContract
{
    /**
     * Lokasi dari aplikasi ini.
     *
     * @var string $path
     */
    private $path;

    /**
     * Init object.
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
    public function path(): string
    {
        return $this->path;
    }

    /**
     * Kirim errornya lewat class.
     *
     * @return string
     */
    public function error(): string
    {
        return \App\Error\Error::class;
    }

    /**
     * Kumpulan middleware yang dijalankan lebih awal.
     *
     * @return array<int, string>
     */
    public function middlewares(): array
    {
        return [
            \App\Middleware\CorsMiddleware::class,
            \App\Middleware\XSSMiddleware::class,
            //\App\Middleware\CsrfMiddleware::class
        ];
    }

    /**
     * Registrasi service agar bisa dijalankan.
     *
     * @return array<int, string>
     */
    public function services(): array
    {
        return [
            \App\Providers\AppServiceProvider::class,
            \App\Providers\RouteServiceProvider::class,
            \App\Providers\TranslatorServiceProvide::class,
        ];
    }
}
