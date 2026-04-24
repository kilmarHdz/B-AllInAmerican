<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\ContactController;
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

Route::prefix('v1')->group(function () {

    // Ruta de prueba / health check
    Route::get('/test', [TestController::class, 'index'])
        ->name('api.test');

    // Formulario de contacto / captación de leads
    Route::post('/contact', [ContactController::class, 'store'])
        ->name('api.contact.store');

});

// Fallback 404
Route::fallback(function () {
    return response()->json([
        'message' => 'Endpoint no encontrado. Verifica la URL y el método HTTP.'
    ], 404);
});
