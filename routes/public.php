<?php

use App\Application\Response;
use App\Application\Router;

$router = Router::getInstance();

// Routes
$router->get('/api', [App\Controllers\HomeController::class, 'index']);