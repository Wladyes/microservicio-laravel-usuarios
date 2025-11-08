<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;


/**
 * Clase base para los controladores del microservicio.
 * Proporciona autorización y validación.
 */
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}

