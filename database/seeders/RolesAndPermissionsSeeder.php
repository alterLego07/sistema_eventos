<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // --- Permisos de Eventos ---
        $eventPermissions = [
            'events.view',
            'events.create',
            'events.edit',
            'events.delete',
            'events.publish',
        ];

        // --- Permisos de Invitaciones ---
        $invitationPermissions = [
            'invitations.view',
            'invitations.create',
            'invitations.edit',
            'invitations.delete',
            'invitations.export',
        ];

        // --- Permisos de Plantillas ---
        $templatePermissions = [
            'templates.view',
            'templates.create',
            'templates.edit',
            'templates.delete',
        ];

        // --- Permisos de Usuarios ---
        $userPermissions = [
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
        ];

        $allPermissions = array_merge(
            $eventPermissions,
            $invitationPermissions,
            $templatePermissions,
            $userPermissions,
        );

        foreach ($allPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // --- Rol: super-admin (acceso total, sin chequeo de permisos) ---
        Role::firstOrCreate(['name' => 'super-admin']);

        // --- Rol: admin (gestión completa de su empresa, incl. usuarios) ---
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions(array_merge(
            $eventPermissions,
            $invitationPermissions,
            $templatePermissions,
            $userPermissions,
        ));

        // --- Rol: organizador (solo sus propios eventos e invitaciones) ---
        $organizador = Role::firstOrCreate(['name' => 'organizador']);
        $organizador->syncPermissions([
            'events.view',
            'events.create',
            'events.edit',
            'invitations.view',
            'invitations.create',
            'invitations.edit',
            'invitations.export',
            'templates.view',
        ]);
    }
}
