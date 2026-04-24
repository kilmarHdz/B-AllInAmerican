<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Configuración CORS para permitir que el frontend Nuxt se comunique
    | con el backend Laravel.
    |
    */

    'paths' => [
        'api/*',                    // Todas las rutas API
        'sanctum/csrf-cookie',      // Cookie CSRF de Sanctum
    ],

    'allowed_methods' => ['*'],     // GET, POST, PUT, DELETE, etc.

    // Soporta múltiples orígenes separados por coma: FRONTEND_URL=https://tu-app.vercel.app,http://localhost:3000
    'allowed_origins' => array_filter(array_map(
        'trim',
        explode(',', env('FRONTEND_URL', 'http://localhost:3000'))
    )),

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,  // IMPORTANTE: true para Sanctum cookies

];
