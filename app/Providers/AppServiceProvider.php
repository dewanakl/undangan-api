<?php

namespace App\Providers;

use App\Repositories\CommentContract;
use App\Repositories\CommentRepositories;
use App\Repositories\LikeContract;
use App\Repositories\LikeRepositories;
use Core\Facades\Provider;

class AppServiceProvider extends Provider
{
    /**
     * Registrasi apa aja disini.
     *
     * @return void
     */
    public function registrasi()
    {
        $this->app->bind(CommentContract::class, CommentRepositories::class);
        $this->app->bind(LikeContract::class, LikeRepositories::class);
    }

    /**
     * Jalankan sewaktu aplikasi dinyalakan.
     *
     * @return void
     */
    public function booting()
    {
        //
    }
}
