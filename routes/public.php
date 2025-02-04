<?php

use App\Application\Router;
use App\Middleware\EnsureInvalidLogin;

$router = Router::getInstance();

// Routes
$router->middleware(EnsureInvalidLogin::class, function () use ($router) {
    $router->get('/api', [App\Controllers\HomeController::class, 'index']);
    $router->post('/api/register', [App\Controllers\AuthController::class, 'register']);
    $router->post('/api/login', [App\Controllers\AuthController::class, 'login']);
});
