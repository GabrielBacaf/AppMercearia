<?php

namespace Database\Factories;

// Importe o modelo original do Spatie
use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoleFactory extends Factory
{
    /**
     * O nome do modelo correspondente da factory.
     *
     * @var string
     */
    protected $model = Role::class; 

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->jobTitle(),
            'guard_name' => 'web',
        ];
    }
}
