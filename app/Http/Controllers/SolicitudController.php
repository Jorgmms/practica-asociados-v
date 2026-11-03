<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Solicitud;

class SolicitudController extends Controller
{
    public function store(Request $request)
    {
        // Validamos los datos de la solicitud
        $request->validate([
            'nombre' => 'required|string|max:255',
            'dpi' => 'required|string|unique:solicituds,dpi|unique:asociados,dpi|max:13',
            'correo' => 'required|email|unique:solicituds,correo|unique:users,email|max:255',
            'telefono' => 'required|string|max:8',
        ]);

        $solicitud = Solicitud::create($request->all());

        return response()->json([
            'message' => 'Solicitud enviada con éxito. Un administrador la revisará pronto.',
            'solicitud' => $solicitud
        ], 201);
    }

    // 2. Listar todas las solicitudes (Solo para el Admin)
    public function index()
    
    {
        return response()->json(Solicitud::where('estado', 'pendiente')->get(), 200);
        
    }

        // 3. Aprobar una solicitud
    public function aprobar($id)
    {
    $solicitud = Solicitud::find($id);
    if (!$solicitud || $solicitud->estado === 'aprobado') {
        return response()->json(['message' => 'Solicitud inválida'], 400);
    }

    // 1. Crear el asociado (igual que antes)
    $asociado = \App\Models\Asociado::create([
        'nombre' => $solicitud->nombre,
        'dpi' => $solicitud->dpi,
        'correo' => $solicitud->correo,
        'telefono' => $solicitud->telefono,
    ]);

    // 2. Generar una contraseña temporal aleatoria (8 caracteres)
    $passwordTemporal = \Illuminate\Support\Str::random(8);

    // 3. Crear el usuario con must_change_password = true
    $user = \App\Models\User::create([
        'name' => $solicitud->nombre,
        'email' => $solicitud->correo,
        'password' => bcrypt($passwordTemporal),
        'role' => 'asociado',
        'must_change_password' => true,
        'password_expires_at' => now()->addHours(24), // La contraseña expira en 24 horas
    ]);

    // 4. Marcar solicitud como aprobada
    $solicitud->update(['estado' => 'aprobado']);

    // 🚨 SIMULACIÓN DE ENVÍO DE CORREO
    // En lugar de enviarlo a Vue, lo escribimos en el log de Laravel.
    \Illuminate\Support\Facades\Log::info("CORREO ENVIADO A: {$user->email} | Usuario: {$user->email} | Contraseña Temporal: {$passwordTemporal}");

    return response()->json([
        'message' => 'Solicitud aprobada. Credenciales enviadas al correo del asociado.',
        'asociado' => $asociado
        // ¡Ya no devolvemos la contraseña a Vue!
    ], 200);
}
}
