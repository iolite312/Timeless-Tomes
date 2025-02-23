<?php

use App\Application\Router;
use App\Middleware\EnsureValidLogin;
use App\Middleware\EnsureInvalidLogin;

$router = Router::getInstance();

$router->get('/api/books/{id}', [App\Controllers\ProductController::class, 'show']);
$router->post('/api/stripe/webhook', [App\Controllers\StripeController::class, 'webhook']);

// Routes
$router->middleware(EnsureInvalidLogin::class, function () use ($router) {
    $router->get('/api', [App\Controllers\HomeController::class, 'index']);
    $router->post('/api/register', [App\Controllers\AuthController::class, 'register']);
    $router->post('/api/login', [App\Controllers\AuthController::class, 'login']);
});
$router->middleware(EnsureValidLogin::class, function () use ($router) {
    $router->get('/api/profile', [App\Controllers\ProfileController::class, 'index']);
    $router->post('/api/profile/update', [App\Controllers\ProfileController::class, 'update']);
    $router->post('/api/profile/delete', [App\Controllers\ProfileController::class, 'delete']);
    $router->post('/api/cart/create', [App\Controllers\StripeController::class, 'createIntent']);
});
