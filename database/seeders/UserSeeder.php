<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verifica se já existe o usuário admin
        $user = User::where('email', 'admin@teste.com')->first();

        if (!$user) {
            $user = User::create([
                'login' => 'teste',
                'name' => 'Administrador',
                'password' => Hash::make('12345678'),
                'email' => 'admin@teste.com',
            ]);

            $user->createToken('admin')->plainTextToken;
        }
    }
}
