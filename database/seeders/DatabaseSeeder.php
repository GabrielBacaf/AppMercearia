<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Whoops\Run;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
        ]);

        // Rodar seeders do banco CENTRAL apenas (ex: Super Admin)
        if (!tenant()) {
            $this->call([
                UserSeeder::class,
            ]);
        }

        // Rodar seeders específicos de tenant (loja) apenas se estivermos num contexto de tenant
        if (tenant()) {
            $this->call([
                CategorySeeder::class,
            ]);
        }
    }
}
