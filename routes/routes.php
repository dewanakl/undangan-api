<?php

use App\Controllers\AuthController;
use App\Controllers\CommentController;
use App\Controllers\WelcomeController;
use App\Middleware\AuthMiddleware;
use Core\Routing\Route;

/**
 * Make something great with this app
 * keep simple yeah.
 */

Route::get('/', WelcomeController::class);

Route::prefix('/api')->group(function () {

    // Login
    Route::prefix('/session')->group(function () {
        Route::post('/', [AuthController::class, 'login']);
        Route::options('/'); // Preflight request [/api/session]
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
