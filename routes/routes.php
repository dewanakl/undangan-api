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
    Route::post('/login', [AuthController::class, 'login']);
    Route::options('/login');

    // Comment
    Route::prefix('/comment')->controller(CommentController::class)->group(function () {

        // Get all
        Route::get('/all', 'all');

        // Must be login
        Route::middleware(AuthMiddleware::class)->group(function () {

            // Get and create comment
            Route::get('/', 'index');
            Route::post('/', 'create');
            Route::options('/');

            Route::prefix('/{id}')->group(function () {

                // Get one
                Route::get('/', 'show');

                // Like comment
                Route::post('/', 'like');
                Route::patch('/', 'unlike');

                // Delete
                Route::delete('/', 'destroy');
                Route::options('/');
            });
        });
    });
});
