<?php

// Define routes
use app\Http\Controllers\Auth\AuthController;
use app\Http\Controllers\HomeController;
use app\Http\Middleware\JWTMiddleware;
use routes\Router;

Router::post('/register', [AuthController::class, 'register']);
Router::post('/login', [AuthController::class, 'login']);

Router::get('/register', [AuthController::class, 'showRegisterForm']);
Router::get('/login', [AuthController::class, 'showLoginForm']);

//protected
Router::get('/', [HomeController::class, 'index'], [JWTMiddleware::class]);