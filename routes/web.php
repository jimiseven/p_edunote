<?php

declare(strict_types=1);

use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\UserController;

$router->get('/', [AuthController::class, 'showLogin']);
$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/logout', [AuthController::class, 'logout']);
$router->get('/dashboard', [DashboardController::class, 'index']);
$router->get('/usuarios', [UserController::class, 'index']);
$router->get('/usuarios/create', [UserController::class, 'create']);
$router->post('/usuarios/store', [UserController::class, 'store']);
$router->get('/usuarios/edit', [UserController::class, 'edit']);
$router->post('/usuarios/update', [UserController::class, 'update']);
$router->post('/usuarios/status', [UserController::class, 'toggleStatus']);
