<?php

namespace Database\Factories;

use App\Enums\CategoryEnum;
use Illuminate\Database\Eloquent\Factories\Factory;


class ProductFactory extends Factory
{

    public function definition(): array
    {
        return [
            'barcode' => $this->faker->unique()->ean13(),
            'name' => $this->faker->unique()->words(2, true),
            'expiration_date' => $this->faker->date('Y-m-d'),
            'category' => $this->faker->randomElement(CategoryEnum::values()),
            'sale_value' => $this->faker->randomFloat(2, 10, 100),
            'stock_quantity' => $this->faker->numberBetween(0, 100),
        ];
    }
}
