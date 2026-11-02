<?php

namespace App\Http\Controllers;

use App\Models\Asociado;
use Illuminate\Http\Request;

class AsociadoController extends Controller
{
    /**
     * leer todos (GEt / api/ asociados)
     */
    public function index()
    {
        $asociados = Asociado::all(); //va a la BD y trae todos los registros
        return response()->json($asociados, 200); // me devuelve los datos en formato JSON con un estado 200 (ok)
    }

    /**
     * Guardar uno nuevo (POST /api/asociados)
     */
    public function store(Request $request)
    {
        // valida que los datos obligatorios vengan y no esten repetidos
        $request->validate([
            'nombre' => 'required|string|max:255',
            'dpi' => 'required|string|unique:asociados,dpi',
            'correo' => 'nullable|email',
            'telefono' => 'nullable|string'
        ]);

        $asociado = Asociado::create($request->all()); // crea el registro en la BD
        return response()->json($asociado, 201); // devuelve el registro creado con estado 201 (created)
    }

    /**
     * Leer uno solo (GET /api/asociados/{id})
     */
    public function show(string $id)
    {
        // busca el asociado por su ID
        $asociado = Asociado::find($id);

        if(!$asociado) {
            return response()->json(['mensaje' => "Asociado no encontrado"], 404); // ERROR 404 (not found)
        }
       return response()->json($asociado, 200);
    }

    /**
     * Acutualizar (PUT /api/asociados/{id})
     */
    public function update(Request $request, string $id)
    {
        $asociado = Asociado::find($id);

        if (!$asociado) {
            return response()->json(['mensaje' => 'Asociado no encontrado'], 404);
        }
    // validamos los datos (ignorando su propio DPI para que no marque error de duplicado)
    $request->validate([
            'nombre' => 'required|string|max:255',
            'dpi' => 'required|string|unique:asociados,dpi,' . $id,
            'correo' => 'nullable|email',
            'telefono' => 'nullable|string'
        ]);

        $asociado->update($request->all()); // Actualiza los datos
        return response()->json($asociado, 200);
    }

    /**
     * Eliminar (Delete /api/asociados/{id})
     */
    public function destroy(string $id)
    {
        //
        $asociado = Asociado::find($id);

        if(!$asociado) {
            return response()->json(['mensaje' => 'Asociado no encontrado'], 404);
        }

        $asociado->delete(); // elimina el registro
        return response()->json(['mensaje' => 'Asociado eliminado correctamente'], 200);
    }
}
