<?php

namespace Database\Factories;

// Importe o modelo correto do Spatie
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Eloquent\Factories\Factory;

class PermissionFactory extends Factory
{
    /**
     * O nome do modelo correspondente da factory.
     *
     * @var string
     */
    protected $model = Permission::class; // <-- Aponte para o modelo Permission

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(), // Gera um nome de permissão
            'guard_name' => 'api',
        ];
    }
}
