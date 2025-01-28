<?php

use App\Application\Router;
use App\Middleware\EnsureInvalidLogin;

$router = Router::getInstance();

// Routes
$router->post('/api/login', [App\Controllers\AuthController::class, 'index']);
$router->middleware(EnsureInvalidLogin::class, function () use ($router) {
    $router->get('/api', [App\Controllers\HomeController::class, 'index']);
});
