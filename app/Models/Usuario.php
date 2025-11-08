<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Modelo: Usuario
 * -----------------
 * Representa la tabla 'usuarios' en la base de datos del microservicio.
 * Este modelo ahora es autenticable (hereda de Authenticatable)
 * y usa Laravel Sanctum para generar tokens personales.
 */
class Usuario extends Authenticatable
{
    // 🔹 Habilita autenticación por tokens (Sanctum) y fábricas de prueba
    use HasApiTokens, HasFactory;

    /**
     * Nombre de la tabla asociada al modelo.
     */
    protected $table = 'usuarios';

    /**
     * Llave primaria de la tabla.
     */
    protected $primaryKey = 'id';

    /**
     * Laravel usa 'created_at' y 'updated_at' automáticamente.
     * Esta propiedad indica que se deben manejar.
     */
    public $timestamps = true;

    /**
     * Campos que pueden asignarse en masa (mass assignment).
     */
    protected $fillable = [
        'nombre',               // Nombre completo del usuario
        'correo',               // Correo electrónico único
        'password',             // Contraseña encriptada
        'rol',                  // Rol del usuario (ADMIN o USER)
        'fecha_nacimiento',     // Fecha de nacimiento (si aplica)
        'sexo',                 // Sexo del usuario
        'numero_seguro',        // Número de seguro médico (si aplica)
        'historial_medico',     // Antecedentes o condiciones médicas
        'contacto_emergencia',  // Teléfono de contacto de emergencia
        'fecha_creacion'        // Fecha de registro
    ];

    /**
     * Campos ocultos al devolver datos JSON.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Conversión automática de tipos de datos.
     */
    protected $casts = [
        'fecha_nacimiento' => 'date',
        'fecha_creacion' => 'datetime',
    ];
}
