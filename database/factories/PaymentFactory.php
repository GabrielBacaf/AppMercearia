<?php

namespace Database\Factories;

use App\Enums\PaymentStatusEnum;
use App\Enums\PaymentTypeEnum;
use Illuminate\Database\Eloquent\Factories\Factory;


class PaymentFactory extends Factory
{
   
    public function definition(): array
    {
        return [

            'value' => $this->faker->randomFloat(2, 20, 1000),

            'payment_type' => $this->faker->randomElement(PaymentTypeEnum::values()),

            'payment_status' => $this->faker->randomElement(PaymentStatusEnum::values()),
        ];

    }
}
