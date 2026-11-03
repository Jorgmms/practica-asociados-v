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
    // CRUD de asociados (Solo el Admin debería poder usar esto, pero por ahora lo dejamos así)
    Route::apiResource('asociados', AsociadoController::class);
    
    // Ruta para que el Admin vea las solicitudes pendientes
    Route::get('/solicitudes', [SolicitudController::class, 'index']);

    //Ruta de aprobar una solicitud (Solo el Admin debería poder usar esto)
    Route::post('/solicitudes/{id}/aprobar', [SolicitudController::class, 'aprobar']);

    // Ruta para cambiar la contraseña (solo el usuario autenticado)
    Route::post('/cambiar-password', [AuthController::class, 'cambiarPassword']);
});