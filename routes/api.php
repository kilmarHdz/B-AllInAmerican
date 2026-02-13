<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\TestController;

/*
|--------------------------------------------------------------------------
| API Routes - Version 1
|--------------------------------------------------------------------------
|
| Rutas de la API para American School.
| Todas las rutas tienen prefijo /api/v1
|
*/

// Grupo de rutas API v1
Route::prefix('v1')->group(function () {
    
    // ============================================
    // RUTAS PÚBLICAS (sin autenticación)
    // ============================================
    
    // Ruta de prueba
    Route::get('/test', [TestController::class, 'index'])
        ->name('api.test');
    
    // Autenticación
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register'])
            ->name('api.auth.register');
        
        Route::post('/login', [AuthController::class, 'login'])
            ->name('api.auth.login');
        
        Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])
            ->name('api.auth.forgot-password');
    });
    
    // ============================================
    // RUTAS PROTEGIDAS (requieren autenticación)
    // ============================================
    
    Route::middleware('auth:sanctum')->group(function () {
        
        // Usuario actual
        Route::get('/user', function (Request $request) {
            return $request->user();
        })->name('api.user');
        
        // Logout
        Route::post('/auth/logout', [AuthController::class, 'logout'])
            ->name('api.auth.logout');
        
        // Aquí agregarás más rutas protegidas según necesites
        // Ejemplo:
        // Route::apiResource('students', StudentController::class);
        // Route::apiResource('teachers', TeacherController::class);
        // Route::apiResource('courses', CourseController::class);
    });
    
});

// ============================================
// FALLBACK ROUTE (404 para rutas no encontradas)
// ============================================

Route::fallback(function () {
    return response()->json([
        'message' => 'Endpoint no encontrado. Verifica la URL y el método HTTP.'
    ], 404);
});
