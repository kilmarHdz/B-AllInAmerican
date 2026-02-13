# 🔧 FASE 1: Configuración Backend Laravel - API + CORS + Autenticación

## Documento de Implementación - All-In American School Backend

**Proyecto**: All-In American School  
**Backend**: Laravel 12  
**Ubicación**: `~/cabrera/web_proyectos/AmericanSchool/backend`  
**Frontend**: Nuxt 4 en `~/cabrera/web_proyectos/AmericanSchool/frontend`  
**Fecha**: Febrero 2026

---

## 📋 Índice

1. [Instalación de Laravel Sanctum](#1-instalación-de-laravel-sanctum)
2. [Configuración de Variables de Entorno](#2-configuración-de-variables-de-entorno)
3. [Creación de Rutas API](#3-creación-de-rutas-api)
4. [Configuración de Bootstrap](#4-configuración-de-bootstrap)
5. [Configuración de CORS](#5-configuración-de-cors)
6. [Creación de Controladores API](#6-creación-de-controladores-api)
7. [Creación de API Resources](#7-creación-de-api-resources)
8. [Migraciones de Base de Datos](#8-migraciones-de-base-de-datos)
9. [Testing de la API](#9-testing-de-la-api)
10. [Troubleshooting](#10-troubleshooting)

---

## 1. Instalación de Laravel Sanctum

### 1.1 Instalar el Paquete

```bash
cd ~/cabrera/web_proyectos/AmericanSchool/backend
composer require laravel/sanctum
```

### 1.2 Publicar Configuración

```bash
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
```

Esto creará:
- `config/sanctum.php` - Configuración de Sanctum
- `database/migrations/xxxx_xx_xx_xxxxxx_create_personal_access_tokens_table.php` - Tabla de tokens

### 1.3 Verificar Instalación

```bash
# Verificar que el archivo de configuración existe
ls -la config/sanctum.php
```

---

## 2. Configuración de Variables de Entorno

### 2.1 Editar `.env`

Abre el archivo `.env` y agrega/modifica estas líneas:

```bash
# URL de la aplicación Laravel
APP_URL=http://localhost:8000

# URL del Frontend Nuxt
FRONTEND_URL=http://localhost:3000

# Dominios autorizados por Sanctum (SPA)
SANCTUM_STATEFUL_DOMAINS=localhost:3000,127.0.0.1:3000

# Configuración de sesión
SESSION_DRIVER=cookie
SESSION_LIFETIME=120
SESSION_DOMAIN=localhost
SESSION_SECURE_COOKIE=false

# Para producción cambiar a:
# SESSION_SECURE_COOKIE=true
# SESSION_DOMAIN=.tudominio.com
```

### 2.2 Crear `.env.example` Actualizado

```bash
cp .env .env.example
# Luego editar .env.example y limpiar valores sensibles
```

---

## 3. Creación de Rutas API

### 3.1 Crear `routes/api.php`

Laravel 12 no crea este archivo por defecto. Créalo manualmente:

```bash
touch routes/api.php
```

### 3.2 Contenido de `routes/api.php`

```php
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
```

---

## 4. Configuración de Bootstrap

### 4.1 Modificar `bootstrap/app.php`

Laravel 12 usa una nueva estructura de configuración. Edita el archivo:

```php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',           // ← AGREGAR ESTA LÍNEA
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        apiPrefix: 'api',                            // ← AGREGAR: Prefijo para rutas API
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Configuración de middleware para API
        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);

        // Alias de middleware personalizados
        $middleware->alias([
            'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        ]);
        
        // CORS se maneja automáticamente en Laravel 12
        // pero podemos personalizar si es necesario
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Aquí puedes personalizar el manejo de excepciones
        // Por ejemplo, formatear errores de validación para la API
    })
    ->create();
```

---

## 5. Configuración de CORS

### 5.1 Verificar/Crear `config/cors.php`

Laravel 12 incluye CORS por defecto, pero verifica que existe:

```bash
ls -la config/cors.php
```

Si NO existe, créalo:

```bash
touch config/cors.php
```

### 5.2 Contenido de `config/cors.php`

```php
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

    'allowed_origins' => [
        env('FRONTEND_URL', 'http://localhost:3000'),
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,  // IMPORTANTE: true para Sanctum cookies

];
```

### 5.3 Actualizar `config/sanctum.php`

Edita el archivo y verifica estas configuraciones:

```php
<?php

return [

    'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', sprintf(
        '%s%s',
        'localhost,localhost:3000,127.0.0.1,127.0.0.1:3000,::1',
        Sanctum::currentApplicationUrlWithPort()
    ))),

    'guard' => ['web'],

    'expiration' => null,

    'token_prefix' => env('SANCTUM_TOKEN_PREFIX', ''),

    'middleware' => [
        'authenticate_session' => Laravel\Sanctum\Http\Middleware\AuthenticateSession::class,
        'encrypt_cookies' => Illuminate\Cookie\Middleware\EncryptCookies::class,
        'validate_csrf_token' => Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
    ],

];
```

---

## 6. Creación de Controladores API

### 6.1 Crear Estructura de Directorios

```bash
mkdir -p app/Http/Controllers/Api/V1
```

### 6.2 Crear `TestController.php`

```bash
php artisan make:controller Api/V1/TestController
```

**Contenido de `app/Http/Controllers/Api/V1/TestController.php`:**

```php
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
```

### 6.3 Crear `AuthController.php`

```bash
php artisan make:controller Api/V1/AuthController
```

**Contenido de `app/Http/Controllers/Api/V1/AuthController.php`:**

```php
<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Registrar nuevo usuario
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Crear token de Sanctum
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Usuario registrado exitosamente',
            'data' => [
                'user' => $user,
                'token' => $token,
            ]
        ], 201);
    }

    /**
     * Iniciar sesión
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales proporcionadas son incorrectas.'],
            ]);
        }

        // Crear token de Sanctum
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Inicio de sesión exitoso',
            'data' => [
                'user' => $user,
                'token' => $token,
            ]
        ], 200);
    }

    /**
     * Cerrar sesión
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        // Eliminar todos los tokens del usuario
        $request->user()->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Sesión cerrada exitosamente'
        ], 200);
    }

    /**
     * Recuperar contraseña (placeholder)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // TODO: Implementar lógica de recuperación de contraseña
        // Por ahora solo validamos que el email existe

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'No encontramos un usuario con ese email.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Te hemos enviado un email con instrucciones para recuperar tu contraseña.'
        ], 200);
    }
}
```

---

## 7. Creación de API Resources

Los API Resources transforman tus modelos Eloquent en JSON de forma consistente.

### 7.1 Crear `UserResource.php`

```bash
php artisan make:resource UserResource
```

**Contenido de `app/Http/Resources/UserResource.php`:**

```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'email_verified_at' => $this->email_verified_at?->toIso8601String(),
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
            // NO exponer password ni remember_token
        ];
    }
}
```

### 7.2 Usar Resources en Controladores (Opcional, pero recomendado)

Puedes actualizar el `AuthController` para usar resources:

```php
use App\Http\Resources\UserResource;

// En el método login:
return response()->json([
    'success' => true,
    'message' => 'Inicio de sesión exitoso',
    'data' => [
        'user' => new UserResource($user),
        'token' => $token,
    ]
], 200);
```

---

## 8. Migraciones de Base de Datos

### 8.1 Ejecutar Migraciones

```bash
# Ejecutar todas las migraciones pendientes (incluyendo Sanctum)
php artisan migrate
```

Esto creará:
- Tabla `users` (ya existente en Laravel)
- Tabla `personal_access_tokens` (de Sanctum)
- Otras tablas del sistema

### 8.2 Verificar Tablas

```bash
# Conectar a la base de datos
php artisan tinker

# Verificar que las tablas existen
>>> \DB::select('SELECT table_name FROM information_schema.tables WHERE table_schema = \'public\'');
```

O usando un cliente de PostgreSQL:

```bash
psql -h pgsql -U sail -d laravel -c "\dt"
```

---

## 9. Testing de la API

### 9.1 Iniciar el Servidor

```bash
cd ~/cabrera/web_proyectos/AmericanSchool/backend
php artisan serve
```

El servidor correrá en: `http://localhost:8000`

### 9.2 Probar Endpoint de Test

```bash
# Con curl
curl http://localhost:8000/api/v1/test

# Respuesta esperada:
{
  "success": true,
  "message": "¡API de American School conectada correctamente!",
  "data": {
    "version": "v1",
    "timestamp": "2026-02-10T...",
    "environment": "local"
  }
}
```

### 9.3 Probar Registro de Usuario

```bash
curl -X POST http://localhost:8000/api/v1/auth/register \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@americanschool.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

### 9.4 Probar Login

```bash
curl -X POST http://localhost:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "test@americanschool.com",
    "password": "password123"
  }'
```

### 9.5 Probar Ruta Protegida

```bash
# Usar el token recibido del login
curl http://localhost:8000/api/v1/user \
  -H "Authorization: Bearer TU_TOKEN_AQUI" \
  -H "Accept: application/json"
```

### 9.6 Probar CORS desde el Frontend

Una vez que el frontend esté configurado, prueba desde la consola del navegador:

```javascript
fetch('http://localhost:8000/api/v1/test', {
  method: 'GET',
  credentials: 'include',
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json'
  }
})
.then(res => res.json())
.then(data => console.log(data))
```

---

## 10. Troubleshooting

### Error: "Route [api.test] not defined"

**Solución**: Limpiar caché de rutas

```bash
php artisan route:clear
php artisan config:clear
php artisan cache:clear
```

### Error CORS: "No 'Access-Control-Allow-Origin' header"

**Solución**: Verificar configuración CORS

1. Verificar que `FRONTEND_URL` está en `.env`
2. Verificar `config/cors.php`
3. Restart del servidor Laravel

```bash
# Limpiar config
php artisan config:clear

# Reiniciar servidor
# Ctrl+C y luego
php artisan serve
```

### Error: "CSRF token mismatch"

**Solución**: Para SPA con Sanctum, primero obtén la cookie CSRF:

```bash
curl http://localhost:8000/sanctum/csrf-cookie \
  -X GET \
  -H "Accept: application/json" \
  -c cookies.txt

# Luego usa esa cookie en requests subsecuentes
curl http://localhost:8000/api/v1/auth/login \
  -X POST \
  -b cookies.txt \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","password":"password"}'
```

### Base de Datos No Conecta

**Solución**: Verificar `.env`

```bash
# Verificar variables de DB
cat .env | grep DB_

# Probar conexión
php artisan tinker
>>> \DB::connection()->getPdo();
```

---

## ✅ Checklist Final - Fase 1

Marca cada ítem al completarlo:

- [ ] ✅ Sanctum instalado (`composer require laravel/sanctum`)
- [ ] ✅ Configuración publicada (`php artisan vendor:publish`)
- [ ] ✅ Variables de entorno configuradas en `.env`
- [ ] ✅ Archivo `routes/api.php` creado
- [ ] ✅ `bootstrap/app.php` modificado
- [ ] ✅ `config/cors.php` configurado
- [ ] ✅ `config/sanctum.php` verificado
- [ ] ✅ `TestController` creado
- [ ] ✅ `AuthController` creado
- [ ] ✅ `UserResource` creado
- [ ] ✅ Migraciones ejecutadas (`php artisan migrate`)
- [ ] ✅ Servidor iniciado (`php artisan serve`)
- [ ] ✅ Endpoint `/api/v1/test` probado y funcional
- [ ] ✅ Registro de usuario probado
- [ ] ✅ Login probado
- [ ] ✅ Ruta protegida `/api/v1/user` probada
- [ ] ✅ CORS verificado (sin errores en browser console)

---

## 📚 Recursos Adicionales

- [Laravel Sanctum Documentation](https://laravel.com/docs/12.x/sanctum)
- [Laravel API Resources](https://laravel.com/docs/12.x/eloquent-resources)
- [CORS Configuration](https://laravel.com/docs/12.x/routing#cors)

---

## 🎯 Próximos Pasos (Fase 2)

Una vez completada esta fase, proceder con:

1. Configurar el frontend Nuxt 4 (crear composables, plugins)
2. Crear páginas de login/registro en Nuxt
3. Probar integración completa frontend-backend

---

**Documento creado**: 10 de Febrero, 2026  
**Última actualización**: 10 de Febrero, 2026  
**Versión**: 1.0
