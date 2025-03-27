<?php

use App\Application\Router;
use App\Middleware\EnsureValidLogin;
use App\Middleware\EnsureInvalidLogin;

$router = Router::getInstance();

$router->get('/api/books/{id}', [App\Controllers\ProductController::class, 'show']);
$router->post('/api/stripe/webhook', [App\Controllers\CartController::class, 'webhook']);

// Routes
$router->middleware(EnsureInvalidLogin::class, function () use ($router) {
    $router->get('/api', [App\Controllers\HomeController::class, 'index']);
    $router->post('/api/register', [App\Controllers\AuthController::class, 'register']);
    $router->post('/api/login', [App\Controllers\AuthController::class, 'login']);
});
$router->middleware(EnsureValidLogin::class, function () use ($router) {
    $router->get('/api/profile', [App\Controllers\ProfileController::class, 'index']);
    $router->get('/api/profile/orders', [App\Controllers\ProfileController::class, 'getAllOrders']);
    $router->put('/api/profile/update', [App\Controllers\ProfileController::class, 'update']);
    $router->delete('/api/profile/delete', [App\Controllers\ProfileController::class, 'delete']);
    $router->post('/api/cart/availability', [App\Controllers\CartController::class, 'checkAvailability']);
    $router->post('/api/cart/create', [App\Controllers\CartController::class, 'createIntent']);
});
