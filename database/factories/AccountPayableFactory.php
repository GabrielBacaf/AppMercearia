<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AccountPayable>
 */
class AccountPayableFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => 'Despesa ' . $this->faker->words(2, true),
            'amount' => $this->faker->randomFloat(2, 50, 5000),
            'due_date' => $this->faker->dateTimeBetween('-1 month', '+1 month')->format('Y-m-d'),
            'paid_date' => $this->faker->optional(0.3)->dateTimeBetween('-1 month', 'now')?->format('Y-m-d'),
            'status' => $this->faker->randomElement(\App\Enums\FinancialStatusEnum::cases())->value,
            'type' => $this->faker->randomElement(\App\Enums\AccountPayableTypeEnum::cases())->value,
        ];
    }
}
