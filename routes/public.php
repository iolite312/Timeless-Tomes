<?php

use App\Enums\RoleEnum;
use App\Application\Router;
use App\Middleware\EnsureValidLogin;
use App\Middleware\EnsureInvalidLogin;
use App\Middleware\EnsureValidRoleAccess;

$router = Router::getInstance();

$router->get('/api/books/{id}', [App\Controllers\ProductController::class, 'show']);
$router->get('/api/search', [App\Controllers\ProductController::class, 'searchKey']);
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
    $router->post('/api/profile/seller', [App\Controllers\ProfileController::class, 'createSellerAccount']);
    $router->middleware(EnsureValidRoleAccess::class, function () use ($router) {
        $router->get('/api/seller/products', [App\Controllers\ProductController::class, 'getAllProducts']);
        $router->post('/api/seller/products', [App\Controllers\ProductController::class, 'createProduct']);
        $router->get('/api/seller/products/{id}', [App\Controllers\ProductController::class, 'show']);
        $router->put('/api/seller/products/{id}', [App\Controllers\ProductController::class, 'updateProduct']);
        $router->delete('/api/seller/products/{id}', [App\Controllers\ProductController::class, 'deleteProduct']);
    }, [[RoleEnum::SELLER, RoleEnum::ADMIN]]);
    $router->middleware(EnsureValidRoleAccess::class, function () use ($router) {
        $router->get('/api/admin/orders', [App\Controllers\AdminController::class, 'getAllOrders']);
        $router->get('/api/admin/users', [App\Controllers\AdminController::class, 'getAllUsers']);
        $router->get('/api/admin/sellers', [App\Controllers\AdminController::class, 'getNonApprovedSellers']);
        $router->put('/api/admin/sellers/{id}', [App\Controllers\AdminController::class, 'approveSeller']);
    }, [[RoleEnum::ADMIN]]);
});
