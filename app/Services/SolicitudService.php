<?php

namespace App\Services;

use App\Models\Solicitud;
use App\Models\User;
use App\Models\Asociado;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Exception;

class SolicitudService
{
    /**
     * Este método se encarga de todo el proceso de aprobar una solicitud.
     * Recibe el ID de la solicitud y devuelve los datos creados.
     */
    public function aprobarSolicitud($id)
    {
        // 1. Buscamos la solicitud en la base de datos
        $solicitud = Solicitud::find($id);

        // 2. Validamos que exista y que no esté ya aprobada
        if (!$solicitud) {
            throw new Exception('Solicitud no encontrada');
        }

        if ($solicitud->estado === 'aprobado') {
            throw new Exception('Esta solicitud ya fue aprobada');
        }

        // 3. Creamos el registro en la tabla de ASOCIADOS
        $asociado = Asociado::create([
            'nombre' => $solicitud->nombre,
            'dpi' => $solicitud->dpi,
            'correo' => $solicitud->correo,
            'telefono' => $solicitud->telefono,
        ]);

        // 4. Generamos una contraseña temporal aleatoria
        $passwordTemporal = Str::random(8);

        // 5. Creamos el USUARIO con su rol y reglas de seguridad
        $user = User::create([
            'name' => $solicitud->nombre,
            'email' => $solicitud->correo,
            'password' => bcrypt($passwordTemporal),
            'role' => 'asociado',
            'must_change_password' => true, // Obligamos a cambiarla
            'password_expires_at' => now()->addHours(24), // Expira en 24 horas
        ]);

        // 6. Actualizamos el estado de la solicitud a 'aprobado'
        $solicitud->update(['estado' => 'aprobado']);

        // 7. Guardamos las credenciales en el LOG (simulando el correo)
        Log::info("CORREO ENVIADO A: {$user->email} | Usuario: {$user->email} | Contraseña Temporal: {$passwordTemporal}");

        // 8. Devolvemos los datos creados al controlador
        return [
            'asociado' => $asociado,
            'user' => $user
        ];
    }
}
