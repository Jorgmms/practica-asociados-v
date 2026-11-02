<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AsociadoController;
use App\Http\Controllers\AuthController; // <-- Movido arriba

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// ruta del CRUD de asociados
// Ruta pública (no necesita token)
Route::post('/login', [AuthController::class, 'login']);

// Rutas protegidas (solo con token)
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('asociados', AsociadoController::class);
});