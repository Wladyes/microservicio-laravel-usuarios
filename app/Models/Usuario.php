<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Modelo Usuario.
 * Representa la tabla 'usuarios' y permite autenticación con Sanctum.
 */
class Usuario extends Authenticatable
{
    use HasApiTokens, HasFactory;

    /**
     * Nombre de la tabla asociada.
     */
    protected $table = 'usuarios';

    /**
     * Llave primaria de la tabla.
     */
    protected $primaryKey = 'id';

    /**
     * Indica si se usan timestamps (created_at, updated_at).
     */
    public $timestamps = true;

    /**
     * Campos que pueden asignarse masivamente.
     */
    protected $fillable = [
        'nombre',
        'correo',
        'password',
        'rol',
        'fecha_nacimiento',
        'sexo',
        'numero_seguro',
        'historial_medico',
        'contacto_emergencia',
        'fecha_creacion'
    ];

    /**
     * Campos ocultos en respuestas JSON.
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
