<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

⚙️ 1. Configuración del Proyecto
🔹 Comandos de instalación
# Crear el proyecto Laravel
composer create-project laravel/laravel microservicio_usuarios

# Ingresar al directorio del proyecto
cd microservicio_usuarios

# Instalar Laravel Sanctum (autenticación por tokens)
composer require laravel/sanctum

🔹 Configurar el archivo .env

Editar la conexión a base de datos y puerto MySQL:

APP_NAME=Laravel
APP_ENV=local
APP_KEY=base64:JkyztwdGkaNqs9HPu8vHFGp7lIK7y8iZEkNaWIki6oA=
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3307
DB_DATABASE=microservicio_usuarios
DB_USERNAME=root
DB_PASSWORD=


💡 Este servicio puede ejecutarse en múltiples puertos simultáneamente, por ejemplo:

php artisan serve --port=8000
php artisan serve --port=8006


Cada instancia mantiene su propio entorno independiente.

🧱 2. Migración y Modelo
🔹 Crear la migración de usuarios
php artisan make:migration create_usuarios_table --create=usuarios

🔹 Migración database/migrations/xxxx_xx_xx_create_usuarios_table.php
Schema::create('usuarios', function (Blueprint $table) {
    $table->id();
    $table->string('nombre', 100);
    $table->string('correo')->unique();
    $table->string('password');
    $table->enum('rol', ['ADMIN', 'USER'])->default('USER');
    $table->date('fecha_nacimiento')->nullable();
    $table->string('sexo', 20)->nullable();
    $table->string('numero_seguro', 100)->nullable();
    $table->text('historial_medico')->nullable();
    $table->string('contacto_emergencia', 30)->nullable();
    $table->timestamps();
});


Ejecutar las migraciones:

php artisan migrate

🧩 3. Modelo Eloquent

Archivo: app/Models/Usuario.php

Define los campos asignables, tipos de datos, y oculta la contraseña en las respuestas JSON.

class Usuario extends Model
{
    use HasFactory;

    protected $table = 'usuarios';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'nombre', 'correo', 'password', 'rol',
        'fecha_nacimiento', 'sexo', 'numero_seguro',
        'historial_medico', 'contacto_emergencia', 'fecha_creacion'
    ];

    protected $hidden = ['password'];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'fecha_creacion' => 'datetime',
    ];
}

🧠 4. Controladores
🔹 Controlador principal UsuarioController.php

Implementa las operaciones CRUD RESTful:

GET /api/usuarios

POST /api/usuarios

GET /api/usuarios/{id}

PUT /api/usuarios/{id}

DELETE /api/usuarios/{id}

Cada método valida los datos y devuelve respuestas JSON consistentes.

🔹 Controlador de Autenticación AuthController.php

Utiliza Laravel Sanctum para registrar, autenticar y cerrar sesión con tokens personales.

Endpoints:

Método	Ruta	Descripción
POST	/api/register	Registro de usuario
POST	/api/login	Inicio de sesión
POST	/api/logout	Cierre de sesión (requiere token)
🗺️ 5. Rutas

Archivo: routes/api.php

use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\AuthController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware('api')->group(function () {
    Route::get('/usuarios', [UsuarioController::class, 'index']);
    Route::post('/usuarios', [UsuarioController::class, 'store']);
    Route::get('/usuarios/{id}', [UsuarioController::class, 'show']);
    Route::put('/usuarios/{id}', [UsuarioController::class, 'update']);
    Route::delete('/usuarios/{id}', [UsuarioController::class, 'destroy']);
});

🔐 6. Autenticación con Sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate


Esto crea la tabla personal_access_tokens para manejar sesiones por token.

🧪 7. Pruebas con Postman
🔹 Registro

POST http://127.0.0.1:8000/api/register

{
  "nombre": "Juan Pérez",
  "correo": "juan8000@example.com",
  "password": "123456",
  "rol": "USER"
}


📥 Respuesta:

{
  "message": "Usuario registrado correctamente.",
  "usuario": { ... },
  "token": "3|tC1yv7cDQ4WxssagPTYavkrR3s9CIx5tdGcXT6t4e2aeec15"
}

🔹 Login

POST http://127.0.0.1:8000/api/login

{
  "correo": "juan8000@example.com",
  "password": "123456"
}


Devuelve un token de acceso personal.

🔹 Logout

POST http://127.0.0.1:8000/api/logout

Encabezado:

Authorization: Bearer <token>

🔹 CRUD Usuarios (protegido opcionalmente por token)
Acción	Método	Ruta	Autenticación
Listar usuarios	GET	/api/usuarios	Opcional
Crear usuario	POST	/api/usuarios	Opcional
Ver usuario	GET	/api/usuarios/{id}	Opcional
Actualizar usuario	PUT	/api/usuarios/{id}	Opcional
Eliminar usuario	DELETE	/api/usuarios/{id}	Opcional
🧭 8. Escalabilidad: múltiples instancias

Este microservicio puede ejecutarse en paralelo en diferentes puertos, sin compartir estado local:

php artisan serve --port=8000
php artisan serve --port=8006


Cada instancia mantiene su propio proceso, autenticación independiente y conexión a la misma base de datos, cumpliendo el requisito de escalabilidad horizontal.

🧰 9. Cumplimiento de Requisitos Técnicos
Requisito	Cumplido	Descripción
Laravel	✅	Proyecto creado con Laravel 12
Arquitectura RESTful	✅	Endpoints CRUD y autenticación
Patrón MVC	✅	Modelos, Controladores y Rutas separados
Rutas en api.php	✅	Todas las rutas REST definidas ahí
Respuestas JSON	✅	response()->json() en todas las salidas
Validaciones	✅	Reglas validate() en controladores
Múltiples instancias	✅	Puertos 8000 y 8006 ejecutados simultáneamente
Autenticación	✅	Implementada con Laravel Sanctum
Pruebas en Postman	✅	Casos de prueba registrados
🗂️ 10. Estructura del Proyecto
microservicio_usuarios/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       ├── AuthController.php
│   │       └── UsuarioController.php
│   ├── Models/
│   │   └── Usuario.php
├── database/
│   └── migrations/
│       └── xxxx_create_usuarios_table.php
├── routes/
│   └── api.php
├── .env
├── composer.json
└── README.md

🚀 11. Despliegue y ejecución
# Instalar dependencias
composer install

# Generar clave de aplicación
php artisan key:generate

# Migrar base de datos
php artisan migrate

# Iniciar servidor en puerto 8000
php artisan serve --port=8000
