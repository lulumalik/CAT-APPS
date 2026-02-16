<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\TestDefinitionController;
use App\Http\Controllers\AuthController;

// Auth routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::get('/user', [AuthController::class, 'user']);
Route::post('/register', [AuthController::class, 'register']);

// Public test routes (accessible to authenticated users)
Route::get('/incoming-tests', [TestDefinitionController::class, 'incoming']);
Route::get('/available-tests', [TestDefinitionController::class, 'available']);

// Test operations (requires authentication via session)
Route::middleware('auth')->group(function () {
    Route::get('/tests/{test}', [TestDefinitionController::class, 'show']);
    Route::post('/tests/{test}/submit', [TestDefinitionController::class, 'submit']);
    Route::get('/my-tests', [TestDefinitionController::class, 'myTests']);
});

Route::middleware('role:admin')->group(function () {
    Route::get('/questions', [QuestionController::class, 'index']);
    Route::post('/questions', [QuestionController::class, 'store']);
    Route::put('/questions/{question}', [QuestionController::class, 'update']);
    Route::delete('/questions/{question}', [QuestionController::class, 'destroy']);

    Route::get('/tests', [TestDefinitionController::class, 'index']);
    Route::post('/tests', [TestDefinitionController::class, 'store']);
    Route::put('/tests/{test}', [TestDefinitionController::class, 'update']);
    Route::delete('/tests/{test}', [TestDefinitionController::class, 'destroy']);
});
