<?php

use Core\Router;

use App\Controllers\AuthController;
use App\Controllers\HomeController;
use App\Controllers\PostController;
use App\Controllers\ProfileController;
use App\Middlewares\AuthMiddleware;

Router::get('/', [HomeController::class, 'index']);

Router::group('', function () {
    Router::get('/home', [HomeController::class, 'index']);
    Router::get('/posts', [PostController::class, 'index']);
    Router::get('/posts/create', [PostController::class, 'create']);
    Router::post('/posts', [PostController::class, 'store']);
    Router::get('/posts/{id}/edit', [PostController::class, 'edit']);
    Router::post('/posts/{id}', [PostController::class, 'update']);
    Router::post('/posts/{id}/delete', [PostController::class, 'delete']);
    Router::get('/profile', [ProfileController::class, 'show']);
    Router::post('/profile', [ProfileController::class, 'update']);
    Router::post('/profile/password', [ProfileController::class, 'password']);
}, AuthMiddleware::class);

Router::get('/login', [AuthController::class, 'login']);
Router::post('/login', [AuthController::class, 'authenticate']);
Router::get('/register', [AuthController::class, 'register']);
Router::post('/register', [AuthController::class, 'store']);
Router::post('/logout', [AuthController::class, 'logout']);

Router::get('/404', fn() => view('404'));
