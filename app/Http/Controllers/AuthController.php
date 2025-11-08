<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'correo' => 'required|string|email|unique:usuarios,correo',
            'password' => 'required|string|min:6',
            'rol' => 'in:ADMIN,USER',
        ]);

        $usuario = Usuario::create([
            'nombre' => $validated['nombre'],
            'correo' => $validated['correo'],
            'password' => Hash::make($validated['password']),
            'rol' => $validated['rol'] ?? 'USER',
        ]);

        $token = $usuario->createToken('token_auth')->plainTextToken;

        return response()->json([
            'message' => 'Usuario registrado correctamente.',
            'usuario' => $usuario,
            'token' => $token,
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'correo' => 'required|email',
            'password' => 'required|string',
        ]);

        $usuario = Usuario::where('correo', $request->correo)->first();

        if (!$usuario || !Hash::check($request->password, $usuario->password)) {
            throw ValidationException::withMessages([
                'correo' => ['Las credenciales no son válidas.'],
            ]);
        }

        $token = $usuario->createToken('token_auth')->plainTextToken;

        return response()->json([
            'message' => 'Inicio de sesión exitoso.',
            'usuario' => $usuario,
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Sesión cerrada correctamente.',
        ]);
    }
}
