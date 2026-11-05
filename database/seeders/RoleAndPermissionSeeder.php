<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Limpiamos la caché de Spatie
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Lista de permisos
        $permisos = [
            'ver-dashboard-admin',
            'crear-asociado',
            'editar-asociado',
            'eliminar-asociado',
            'ver-solicitudes',
            'aprobar-solicitud',
            'ver-perfil-propio',
            'gestionar-usuarios'
        ];

        // 3. Creamos los permisos (si no existen)
        foreach ($permisos as $permiso) {
            Permission::firstOrCreate(['name' => $permiso, 'guard_name' => 'web']);
        }

        // 4. RESET DE CACHÉ (¡Vital! Para que Spatie encuentre los permisos recién creados)
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 5. Creamos los roles y asignamos permisos
        $superadmin = Role::firstOrCreate(['name' => 'superadmin', 'guard_name' => 'web']);
        $superadmin->syncPermissions(Permission::all()); // Le da todos

        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->syncPermissions([
            'ver-dashboard-admin',
            'crear-asociado',
            'editar-asociado',
            'eliminar-asociado',
            'ver-solicitudes',
            'aprobar-solicitud'
        ]);

        $asociado = Role::firstOrCreate(['name' => 'asociado', 'guard_name' => 'web']);
        $asociado->syncPermissions(['ver-perfil-propio']);

        // 6. Asignamos el rol superadmin al usuario Admin
        $user = User::where('email', 'admin@test.com')->first();
        if ($user) {
            $user->assignRole('superadmin');
        }
    }
}