<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\QuestionController;
use App\Http\Controllers\Api\QuizController;
use App\Http\Controllers\Api\UserController;
use App\Models\Quiz;
use Illuminate\Support\Facades\Route;

// Rotas de acesso livre (não requerem autenticação).
    // Rotas para login e logout de usuários e administradores
    Route::post('/login/admin', [AuthController::class, 'adminLogin']);
    Route::post('/login/user', [AuthController::class, 'userLogin']);
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Rota para registro de um novo usuário
    Route::post('/register/user', [UserController::class, 'store']);
    
// Rotas protegidas (requerem autenticação via Sanctum)
Route::middleware('auth:sanctum')->group(function () {
        // Rotas acessíveis para todos os usuários autenticados
        Route::get('quizzes', [QuizController::class, 'index']);
        Route::get('quizzes/{quiz}', [QuizController::class, 'show']);
        Route::get('quizzes/{quizId}/questions', [QuestionController::class, 'index']);
        Route::get('quizzes/{quizId}/questions/{id}', [QuestionController::class, 'show']);
    
        Route::middleware('isAdmin')->group(function () {
            // Rotas de gerenciamento de usuários, acessíveis apenas para usuários autenticados
            Route::apiResource('users', UserController::class)->except(['store']); // 'store' é excluído, pois está acessível sem autenticação
            Route::post('quizzes', [QuizController::class, 'store']);
            Route::patch('quizzes/{quiz}', [QuizController::class, 'update']);
            Route::delete('quizzes/{quiz}', [QuizController::class, 'destroy']);
            Route::post('quizzes/{quizId}/questions', [QuestionController::class, 'store']);
            Route::patch('questions/{id}', [QuestionController::class, 'update']);
            Route::delete('questions/{id}', [QuestionController::class, 'destroy']);
            Route::apiResource('quizzes.questions', QuestionController::class)->shallow();
        
        });
    
});
