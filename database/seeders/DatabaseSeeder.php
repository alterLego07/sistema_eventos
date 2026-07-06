<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            TemplateSeeder::class,
            RolesAndPermissionsSeeder::class,
            AdminUserSeeder::class,
            CompanySeeder::class,
        ]);
    }
}
