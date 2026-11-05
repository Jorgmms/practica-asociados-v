<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AsociadoController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SolicitudController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// --- RUTAS PÚBLICAS ---
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']); // (Podemos dejarla por si acaso, aunque ya usaremos solicitudes)
Route::post('/solicitar-afiliacion', [SolicitudController::class, 'store']); // Ruta para enviar solicitudes
Route::get('/solicitudes', [SolicitudController::class, 'index']);
Route::post('/solicitudes/{id}/aprobar', [SolicitudController::class, 'aprobar']); // 

// --- RUTAS PROTEGIDAS (Solo con Token) ---
Route::middleware('auth:sanctum')->group(function () {
    
    // Rutas de Dashboard (Solo Admins)
    Route::get('/dashboard-stats', [\App\Http\Controllers\DashboardController::class, 'index'])
        ->middleware('permission:ver-dashboard-admin');

    // Rutas de Asociados
    Route::get('/asociados', [AsociadoController::class, 'index']); // Todos pueden ver (ajustaremos luego)
    Route::post('/asociados', [AsociadoController::class, 'store'])->middleware('permission:crear-asociado');
    Route::put('/asociados/{id}', [AsociadoController::class, 'update'])->middleware('permission:editar-asociado');
    Route::delete('/asociados/{id}', [AsociadoController::class, 'destroy'])->middleware('permission:eliminar-asociado');

    // Rutas de Solicitudes
    Route::get('/solicitudes', [SolicitudController::class, 'index'])->middleware('permission:ver-solicitudes');
    Route::post('/solicitudes/{id}/aprobar', [SolicitudController::class, 'aprobar'])->middleware('permission:aprobar-solicitud');

    // Ruta para cambiar contraseña (Cualquier usuario autenticado)
    Route::post('/cambiar-password', [AuthController::class, 'cambiarPassword']);
});