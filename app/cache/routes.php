<?php return array (
  0 => 
  array (
    'method' => 'GET',
    'path' => '/',
    'controller' => NULL,
    'function' => 'App\\Controllers\\WelcomeController',
    'middleware' => 
    array (
    ),
    'name' => NULL,
  ),
  1 => 
  array (
    'method' => 'POST',
    'path' => '/api/login',
    'controller' => 'App\\Controllers\\AuthController',
    'function' => 'login',
    'middleware' => 
    array (
    ),
    'name' => NULL,
  ),
  2 => 
  array (
    'method' => 'OPTIONS',
    'path' => '/api/login',
    'controller' => NULL,
    'function' => NULL,
    'middleware' => 
    array (
    ),
    'name' => NULL,
  ),
  3 => 
  array (
    'method' => 'GET',
    'path' => '/api/comment/all',
    'controller' => 'App\\Controllers\\CommentController',
    'function' => 'all',
    'middleware' => 
    array (
    ),
    'name' => NULL,
  ),
  4 => 
  array (
    'method' => 'GET',
    'path' => '/api/comment',
    'controller' => 'App\\Controllers\\CommentController',
    'function' => 'index',
    'middleware' => 
    array (
      0 => 'App\\Middleware\\AuthMiddleware',
    ),
    'name' => NULL,
  ),
  5 => 
  array (
    'method' => 'POST',
    'path' => '/api/comment',
    'controller' => 'App\\Controllers\\CommentController',
    'function' => 'create',
    'middleware' => 
    array (
      0 => 'App\\Middleware\\AuthMiddleware',
    ),
    'name' => NULL,
  ),
  6 => 
  array (
    'method' => 'OPTIONS',
    'path' => '/api/comment',
    'controller' => 'App\\Controllers\\CommentController',
    'function' => NULL,
    'middleware' => 
    array (
      0 => 'App\\Middleware\\AuthMiddleware',
    ),
    'name' => NULL,
  ),
  7 => 
  array (
    'method' => 'DELETE',
    'path' => '/api/comment/([\\w-]*)',
    'controller' => 'App\\Controllers\\CommentController',
    'function' => 'destroy',
    'middleware' => 
    array (
      0 => 'App\\Middleware\\AuthMiddleware',
    ),
    'name' => NULL,
  ),
  8 => 
  array (
    'method' => 'OPTIONS',
    'path' => '/api/comment/([\\w-]*)',
    'controller' => 'App\\Controllers\\CommentController',
    'function' => NULL,
    'middleware' => 
    array (
      0 => 'App\\Middleware\\AuthMiddleware',
    ),
    'name' => NULL,
  ),
);