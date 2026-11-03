<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    // 1. FUNCIÓN DE LOGIN
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Credenciales inválidas'
            ], 401);
        }

        $user = User::where('email', $request->email)->firstOrFail();

        // Verificamos si debe cambiar la contraseña y si ya expiró
        if ($user->must_change_password && $user->password_expires_at && now()->greaterThan($user->password_expires_at)) {
            return response()->json([
                'message' => 'Tu contraseña temporal ha expirado. Contacta al administrador para que te genere una nueva.'
            ], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
            'must_change_password' => $user->must_change_password // <-- ¡AQUÍ ESTABA EL ERROR!
        ]);
    }

    // 2. FUNCIÓN DE REGISTRO (Para usuarios rápidos, si la usas)
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'asociado',
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ], 201);
    }

    // 3. FUNCIÓN PARA CAMBIAR LA CONTRASEÑA
    public function cambiarPassword(Request $request)
    {
        $request->validate(['password' => 'required|string|min:6']);
        
        $user = $request->user();
        $user->password = bcrypt($request->password);
        $user->must_change_password = false; // Ya no necesita cambiarla
        $user->save();

        return response()->json(['message' => 'Contraseña actualizada']);
    }
}