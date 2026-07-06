<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@sistema-invitaciones.com'],
            [
                'name' => 'Administrador',
                'email' => 'admin@sistema-invitaciones.com',
                'password' => Hash::make('Admin1234!'),
                'email_verified_at' => now(),
            ],
        );

        $admin->assignRole('super-admin');

        $this->command->info("Admin creado: {$admin->email} / Admin1234!");
    }
}
