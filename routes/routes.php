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

    Route::controller(CommentController::class)->prefix('/comment')->group(function () {
        Route::get('/', 'index');

        Route::middleware(AuthMiddleware::class)->group(function () {
            Route::post('/', 'create');
            Route::delete('/{id}', 'destroy');
        });
    });
});
