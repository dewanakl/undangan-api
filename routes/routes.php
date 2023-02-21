<?php

use App\Controllers\AuthController;
use App\Controllers\CommentController;
use App\Controllers\WelcomeController;
use App\Middleware\AuthMiddleware;
use Core\Routing\Route;

/**
 * Make something great with this app
 * keep simple yahh
 */

Route::get('/', WelcomeController::class);

Route::prefix('/api')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::options('/login');

    Route::prefix('/comment')->controller(CommentController::class)->group(function () {
        Route::get('/all', 'all');

        Route::middleware(AuthMiddleware::class)->group(function () {
            Route::get('/', 'index');
            Route::post('/', 'create');
            Route::options('/');

            Route::get('/{id}', 'show');
            Route::delete('/{id}', 'destroy');
            Route::options('/{id}');
        });
    });
});
