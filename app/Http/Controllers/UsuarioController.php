<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

/**
 * Controlador para la gestión de usuarios.
 * Implementa operaciones CRUD sobre la entidad Usuario.
 */
class UsuarioController extends Controller
{
    /**
     * Devuelve la lista de todos los usuarios registrados.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $usuarios = Usuario::all();
        return response()->json($usuarios, 200);
    }

    /**
     * Crea un nuevo usuario tras validar los datos recibidos.
     * La contraseña se encripta antes de guardar por seguridad.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Validación de los datos de entrada
        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'correo' => 'required|email|unique:usuarios,correo',
            'password' => 'required|string|min:6',
            'fecha_nacimiento' => 'nullable|date',
            'sexo' => 'nullable|in:Masculino,Femenino,Otro',
            'numero_seguro' => 'nullable|string|max:100',
            'historial_medico' => 'nullable|string',
            'contacto_emergencia' => 'nullable|string|max:30',
            'rol' => 'nullable|in:ADMIN,USER',
        ]);

        // Encriptar la contraseña antes de guardar
        $validated['password'] = Hash::make($validated['password']);
        $usuario = Usuario::create($validated);

        return response()->json($usuario, 201);
    }

    /**
     * Muestra un usuario específico por su ID.
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $usuario = Usuario::find($id);
        if (!$usuario) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }
        return response()->json($usuario, 200);
    }

    /**
     * Actualiza los datos de un usuario existente.
     * Solo se actualizan los campos enviados en la petición.
     * Si se actualiza la contraseña, se vuelve a encriptar.
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $usuario = Usuario::find($id);
        if (!$usuario) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        // Validación condicional: solo valida los campos enviados
        $validated = $request->validate([
            'nombre' => 'sometimes|string|max:100',
            'correo' => [
                'sometimes',
                'email',
                // Permite que el usuario conserve su propio correo
                Rule::unique('usuarios', 'correo')->ignore($id),
            ],
            'password' => 'sometimes|string|min:6',
            'fecha_nacimiento' => 'nullable|date',
            'sexo' => 'nullable|in:Masculino,Femenino,Otro',
            'numero_seguro' => 'nullable|string|max:100',
            'historial_medico' => 'nullable|string',
            'contacto_emergencia' => 'nullable|string|max:30',
            'rol' => 'nullable|in:ADMIN,USER',
        ]);

        // Si se envía una nueva contraseña, se encripta
        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $usuario->update($validated);
        return response()->json($usuario, 200);
    }

    /**
     * Elimina un usuario existente por su ID.
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $usuario = Usuario::find($id);
        if (!$usuario) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        $usuario->delete();
        return response()->json(['message' => 'Usuario eliminado'], 200);
    }
}
