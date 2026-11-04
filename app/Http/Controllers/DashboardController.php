<?php

namespace App\Http\Controllers;

use App\Models\Asociado;
use App\Models\Solicitud;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        // Obtenemos los totales de la base de datos
        $totalAsociados = Asociado::count();
        $solicitudesPendientes = Solicitud::where('estado', 'pendiente')->count();
        $totalUsuarios = User::count();

        return response()->json([
            'asociados' => $totalAsociados,
            'solicitudes' => $solicitudesPendientes,
            'usuarios' => $totalUsuarios,
        ], 200);
    }
}