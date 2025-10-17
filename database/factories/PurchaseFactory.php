<?php

namespace Database\Factories;

use App\Enums\StatusEnum;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Purchase>
 */
class PurchaseFactory extends Factory
{

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'purchase_date' => $this->faker->date(),
            'status' => $this->faker->randomElement(StatusEnum::values()),
            'count_value' => null,
            'updated_by' => null,
        ];
    }
}
