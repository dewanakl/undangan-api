<?php

use App\Controllers\Api\AuthController;
use App\Controllers\Api\CommentController;
use App\Controllers\Api\DashboardController;
use App\Middleware\AuthMiddleware;
use App\Middleware\DashboardMiddleware;
use Core\Routing\Route;

/**
 * Make something great with this app
 * keep simple yeah.
 */

Route::prefix('/session')->group(function () {
    Route::post('/', [AuthController::class, 'login']);
    Route::options('/'); // Preflight request [/api/session]
});

Route::middleware(AuthMiddleware::class)->group(function () {

    // Dashboard
    Route::middleware(DashboardMiddleware::class)->group(function () {
        Route::get('/download', [DashboardController::class, 'download']);
        Route::options('/download');

        Route::get('/stats', [DashboardController::class, 'stats']);
        Route::options('/stats');

        Route::put('/key', [DashboardController::class, 'rotate']);
        Route::options('/key');

        Route::get('/user', [DashboardController::class, 'user']);
        Route::patch('/user', [DashboardController::class, 'update']);
        Route::options('/user');
    });

    Route::get('/config', [DashboardController::class, 'config']);
    Route::options('/config'); // Preflight request [/api/config]

    // Comment
    Route::prefix('/comment')->group(function () {

        Route::controller(CommentController::class)->group(function () {
            Route::get('/', 'get');
            Route::post('/', 'create');
        });

        Route::options('/'); // Preflight request [/api/comment]

        Route::prefix('/{id}')->group(function () {
            Route::controller(CommentController::class)->group(function () {

                Route::get('/', 'show');
                Route::put('/', 'update');
                Route::delete('/', 'destroy');

                // Like or unlike comment
                Route::post('/', 'like');
                Route::patch('/', 'unlike');
            });

            Route::options('/'); // Preflight request [/api/comment/{id}]
        });
    });
});
