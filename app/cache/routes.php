<?php return array (
  0 => 
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
  1 => 
  array (
    'method' => 'GET',
    'path' => '/api/comment',
    'controller' => 'App\\Controllers\\CommentController',
    'function' => 'index',
    'middleware' => 
    array (
    ),
    'name' => NULL,
  ),
  2 => 
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
  3 => 
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
);