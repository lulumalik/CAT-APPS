<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\TestDefinitionController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MaterialController;

// Public material routes
Route::get('/materials/public', [MaterialController::class, 'publicIndex']);
Route::get('/materials/public/{slug}', [MaterialController::class, 'publicShow']);

// Auth routes
Route::get('/force-setup', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        \Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]);
        
        // Pastikan admin ada
        \App\Models\User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role' => 'admin',
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Database migrated, seeded, and admin user created successfully.',
            'admin_check' => \App\Models\User::where('email', 'admin@example.com')->exists()
        ]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
});

Route::get('/debug-auth', function () {
    try {
        $dbStatus = \Illuminate\Support\Facades\DB::connection()->getPdo() ? 'Connected' : 'Failed';
        $user = \App\Models\User::where('email', 'admin@example.com')->first();
        if (!$user) {
            return response()->json(['error' => 'User admin@example.com not found. DB Status: ' . $dbStatus]);
        }
        $hashMatch = \Illuminate\Support\Facades\Hash::check('password', $user->password);
        return response()->json([
            'db_status' => $dbStatus,
            'user_found' => true,
            'password_hash' => $user->password,
            'hash_match' => $hashMatch,
            'session_driver' => config('session.driver'),
        ]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
});

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
    Route::apiResource('users', UserController::class);
});

Route::middleware('role:admin,mentor')->group(function () {
    Route::get('/questions', [QuestionController::class, 'index']);
    Route::post('/questions', [QuestionController::class, 'store']);
    Route::put('/questions/{question}', [QuestionController::class, 'update']);
    Route::delete('/questions/{question}', [QuestionController::class, 'destroy']);

    Route::get('/tests', [TestDefinitionController::class, 'index']);
    Route::post('/tests', [TestDefinitionController::class, 'store']);
    Route::put('/tests/{test}', [TestDefinitionController::class, 'update']);
    Route::delete('/tests/{test}', [TestDefinitionController::class, 'destroy']);

    Route::get('/tests/{test}/submissions', [TestDefinitionController::class, 'testSubmissions']);
    Route::put('/submissions/{submission}', [TestDefinitionController::class, 'updateSubmission']);

    Route::apiResource('materials', MaterialController::class)->except(['show']);
    Route::get('/materials/{material}', [MaterialController::class, 'showAdmin']);
});
