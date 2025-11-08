<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UsuarioController extends Controller
{
    public function index()
    {
        $usuarios = Usuario::all();
        return response()->json($usuarios, 200);
    }

    public function store(Request $request)
    {
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

        $validated['password'] = Hash::make($validated['password']);
        $usuario = Usuario::create($validated);

        return response()->json($usuario, 201);
    }

    public function show($id)
    {
        $usuario = Usuario::find($id);
        if (!$usuario) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }
        return response()->json($usuario, 200);
    }

    public function update(Request $request, $id)
    {
        $usuario = Usuario::find($id);
        if (!$usuario) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        $validated = $request->validate([
            'nombre' => 'sometimes|string|max:100',
            'correo' => [
                'sometimes',
                'email',
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

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $usuario->update($validated);
        return response()->json($usuario, 200);
    }

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
