<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

// Rotas de acesso livre (não requerem autenticação).
    // Rotas para login e logout de usuários e administradores
    Route::post('/login/admin', [AuthController::class, 'adminLogin']);
    Route::post('/login/user', [AuthController::class, 'userLogin']);
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Rota para registro de um novo usuário
    Route::post('/register/user', [UserController::class, 'store']);

// Rotas protegidas (requerem autenticação via Sanctum)
Route::middleware(['auth:sanctum'])->group(function () {
    // Rotas de gerenciamento de usuários, acessíveis apenas para usuários autenticados
    Route::apiResource('users', UserController::class)->except(['store']); // 'store' é excluído, pois está acessível sem autenticação
});
