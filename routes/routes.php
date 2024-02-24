<?php

use App\Controllers\Api\AuthController;
use App\Controllers\Api\CommentController;
use App\Controllers\Api\DashboardController as ApiDashboardController;
use App\Controllers\DashboardController;
use App\Controllers\WelcomeController;
use App\Middleware\AuthMiddleware;
use App\Middleware\DashboardMiddleware;
use Core\Routing\Route;

/**
 * Make something great with this app
 * keep simple yeah.
 */

Route::get('/', WelcomeController::class);
Route::get('/dashboard', DashboardController::class);

Route::prefix('/api')->group(function () {

    // Dashboard
    Route::prefix('/dashboard')->group(function () {
        Route::prefix('/session')->group(function () {
            Route::post('/', [AuthController::class, 'login']);
            Route::options('/'); // Preflight request [/api/session]
        });

        Route::middleware([AuthMiddleware::class, DashboardMiddleware::class])->group(function () {
            Route::get('/stats', [ApiDashboardController::class, 'stats']);
            Route::options('/stats');
            Route::get('/download', [ApiDashboardController::class, 'download']);

            Route::get('/key', [ApiDashboardController::class, 'key']);
            Route::put('/key', [ApiDashboardController::class, 'update']);
            Route::options('/key');
        });
    });

    // Comment
    Route::prefix('/comment')->middleware(AuthMiddleware::class)->group(function () {

        // Get and create comment
        Route::controller(CommentController::class)->group(function () {
            Route::get('/', 'get');
            Route::post('/', 'create');
        });

        Route::options('/'); // Preflight request [/api/comment]

        Route::prefix('/{id}')->group(function () {
            Route::controller(CommentController::class)->group(function () {

                // Get one
                Route::get('/', 'show');

                // Update comment
                Route::put('/', 'update');

                // Like or unlike comment
                Route::post('/', 'like');
                Route::patch('/', 'unlike');

                // Delete
                Route::delete('/', 'destroy');
            });

            Route::options('/'); // Preflight request [/api/comment/{id}]
        });
    });
});
