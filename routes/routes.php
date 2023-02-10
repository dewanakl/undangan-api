<?php

use App\Controllers\AuthController;
use App\Controllers\CommentController;
use App\Middleware\AuthMiddleware;
use Core\Routing\Route;

/**
 * Make something great with this app
 * keep simple yahh
 */

Route::prefix('/api')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::options('/login');

    Route::controller(CommentController::class)->prefix('/comment')->group(function () {
        Route::get('/all', 'all');

        Route::middleware(AuthMiddleware::class)->group(function () {
            Route::get('/', 'index');
            Route::post('/', 'create');
            Route::options('/');

            Route::delete('/{id}', 'destroy');
            Route::options('/{id}');
        });
    });
});
