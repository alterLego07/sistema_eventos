<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Event;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        // Empresa demo
        $company = Company::firstOrCreate(
            ['slug' => 'empresa-demo'],
            [
                'name' => 'Empresa Demo',
                'email' => 'contacto@empresa-demo.com',
                'phone' => '+52 55 0000 0000',
                'active' => true,
            ],
        );

        // Admin de la empresa demo
        $admin = User::updateOrCreate(
            ['email' => 'admin@empresa-demo.com'],
            [
                'company_id' => $company->id,
                'name' => 'Admin Empresa Demo',
                'password' => Hash::make('Admin1234!'),
                'email_verified_at' => now(),
            ],
        );
        $admin->assignRole('admin');

        // Organizador de la empresa demo
        $organizador = User::updateOrCreate(
            ['email' => 'organizador@empresa-demo.com'],
            [
                'company_id' => $company->id,
                'name' => 'Organizador Demo',
                'password' => Hash::make('Admin1234!'),
                'email_verified_at' => now(),
            ],
        );
        $organizador->assignRole('organizador');

        // Backfill: cualquier evento/invitación sin empresa se asigna a la demo
        // (para no perder datos previos a la introducción de multi-empresa).
        Event::withoutGlobalScopes()->whereNull('company_id')->update(['company_id' => $company->id]);
        Invitation::withoutGlobalScopes()->whereNull('company_id')->update(['company_id' => $company->id]);

        $this->command->info("Empresa demo creada: {$company->name}");
        $this->command->info("  Admin:       admin@empresa-demo.com / Admin1234!");
        $this->command->info("  Organizador: organizador@empresa-demo.com / Admin1234!");
    }
}
