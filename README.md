<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>



#  README — Microservicio de Usuarios en Laravel

## 1. Descripción General

Este proyecto implementa un **microservicio RESTful para la gestión de usuarios** utilizando **Laravel 12.37.0**, siguiendo el patrón **MVC (Modelo–Vista–Controlador)** y el enfoque **API First**.
Está diseñado para ser **escalable horizontalmente**, ejecutándose en **múltiples instancias** sin compartir estado, y utiliza **Laravel Sanctum** para la autenticación basada en tokens personales.

El sistema devuelve **todas las respuestas en formato JSON**, incluye **validaciones de entrada**, y expone endpoints CRUD seguros y estandarizados.

---

## 2. Objetivos Técnicos

* Desarrollar una API RESTful en Laravel.
* Implementar autenticación sin sesiones mediante Laravel Sanctum.
* Organizar el código bajo el patrón MVC.
* Asegurar validaciones de entrada y respuestas JSON.
* Permitir múltiples instancias independientes para escalabilidad.
* Probar todas las operaciones mediante Postman.

---

## 3. Estructura del Proyecto

Generada mediante:

```
composer create-project laravel/laravel microservicio_usuarios
```

Estructura principal:

```
microservicio_usuarios/
 ├── app/
 │   ├── Http/
 │   │   └── Controllers/
 │   └── Models/
 ├── routes/
 │   └── api.php
 ├── database/
 │   └── migrations/
 ├── .env
 └── artisan
```

---

## 4. Configuración de Base de Datos

Se configuró el archivo `.env` para conexión local a MySQL:

* `DB_CONNECTION=mysql`
* `DB_HOST=127.0.0.1`
* `DB_PORT=3307`
* `DB_DATABASE=microservicio_usuarios`
* `DB_USERNAME=root`
* `DB_PASSWORD=`

Base de datos creada mediante:

```
CREATE DATABASE microservicio_usuarios;
```

También se definieron los drivers:

```
QUEUE_CONNECTION=database
SESSION_DRIVER=array
CACHE_STORE=array
```

Esto evita el almacenamiento de sesiones entre instancias (requisito de escalabilidad).

---

## 5. Migraciones y Modelo de Usuario

Se generó el modelo `Usuario` junto con su migración:

```
php artisan make:model Usuario -m
```

La migración define la tabla `usuarios` con campos comunes y adicionales (rol, sexo, historial médico, etc.).
El modelo `Usuario` hereda de `Authenticatable` y usa `HasApiTokens` para integrarse con Sanctum.

**Explicación de componentes del modelo:**

* **Authenticatable:** permite usar autenticación por tokens.
* **HasApiTokens:** genera y valida tokens de Sanctum.
* **$fillable:** define los campos insertables desde peticiones JSON.
* **$hidden:** oculta la contraseña y el token en las respuestas.
* **$casts:** convierte tipos de datos (por ejemplo, fechas) automáticamente.

---

## 6. Instalación y Configuración de Laravel Sanctum

Se instaló con:

```
composer require laravel/sanctum
```

Luego se publicaron las migraciones:

```
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate
```

Esto creó la tabla `personal_access_tokens` que almacena los tokens de acceso.

En **bootstrap/app.php** se configuró el middleware:

```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->statefulApi();
})
```

Esto permite manejar APIs stateful o stateless sin sesiones compartidas.

---

## 7. Controladores

### a. Controlador Base

Ubicación: `app/Http/Controllers/Controller.php`
Centraliza autorización (`AuthorizesRequests`) y validación (`ValidatesRequests`).

### b. Controlador de Autenticación

`AuthController` implementa:

* **register()**: registra usuarios y genera tokens.
* **login()**: valida credenciales y devuelve nuevo token.
* **logout()**: invalida el token actual.

Incluye validaciones:

* Formato de correo electrónico.
* Longitud mínima de contraseña.
* Restricción de roles permitidos (`ADMIN` o `USER`).

### c. Controlador de Usuarios

`UsuarioController` gestiona el CRUD:

* `index()`: lista todos los usuarios.
* `store()`: crea un nuevo usuario con validaciones.
* `show($id)`: obtiene un usuario por ID.
* `update()`: actualiza datos con validaciones condicionales (`sometimes`).
* `destroy()`: elimina usuarios.

---

## 8. Rutas API

Definidas en `routes/api.php`, siguiendo estructura RESTful:

* `/register`, `/login`, `/logout` → autenticación (controlador `AuthController`).
* `/usuarios` → operaciones CRUD (controlador `UsuarioController`).
* Middleware `auth:sanctum` protege el cierre de sesión y rutas seguras.

---

## 9. Pruebas en Postman (Flujo Completo)

### Escenario

Dos instancias corriendo en paralelo:

| Instancia | URL Base                    | Puerto | Descripción |
| --------- | --------------------------- | ------ | ----------- |
| A         | `http://127.0.0.1:8000/api` | 8000   | Principal   |
| B         | `http://127.0.0.1:8006/api` | 8006   | Réplica     |

### Flujo por instancia

1. **Registro** → POST `/register`
   Se crea usuario y genera token.
2. **Inicio de sesión** → POST `/login`
   Devuelve un token de acceso.
3. **Acceso a recursos** → GET `/usuarios`
   Requiere encabezado `Authorization: Bearer <token>`.
4. **Cierre de sesión** → POST `/logout`
   Invalida el token actual.

### Validación de independencia

* Un token emitido en la instancia A no es válido en la instancia B (`401 Unauthorized`).
* Cada instancia maneja sus tokens de manera aislada (cumpliendo el requisito de escalabilidad horizontal).

---

## 10. Limpieza y Optimización

Antes de ejecutar pruebas:

```
php artisan optimize:clear
```

Esto asegura que no haya rutas o configuraciones en caché que afecten las pruebas.

---

## 11. Cumplimiento de Requisitos Técnicos

| Requisito             | Cumple | Descripción                                              |
| --------------------- | :----: | -------------------------------------------------------- |
| Laravel Framework     |    ✔   | Proyecto creado con Laravel 12.37                        |
| Arquitectura RESTful  |    ✔   | Endpoints HTTP CRUD                                      |
| Patrón MVC            |    ✔   | Separación clara de modelo, controlador y rutas          |
| Rutas en api.php      |    ✔   | Todas las rutas definidas en este archivo                |
| Respuestas JSON       |    ✔   | Todas las respuestas estructuradas en JSON               |
| Validaciones de datos |    ✔   | Reglas implementadas con `$request->validate()`          |
| Autenticación         |    ✔   | Implementada con Sanctum (tokens personales)             |
| Escalabilidad         |    ✔   | Múltiples instancias (8000 y 8006) sin estado compartido |
| Pruebas               |    ✔   | Verificadas mediante Postman                             |
| Manejo de errores     |    ✔   | Validaciones y excepciones controladas                   |

---
## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

