<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TestController extends Controller
{
    /**
     * Endpoint de prueba para verificar la conexión API
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => '¡API de American School conectada correctamente!',
            'data' => [
                'version' => 'v1',
                'timestamp' => now()->toIso8601String(),
                'environment' => app()->environment(),
            ]
        ], 200);
    }
}
