<?php
namespace Database\Factories;

use App\Models\Sale;
use App\Models\User;
use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

class SaleFactory extends Factory
{
    protected $model = Sale::class;

    public function definition(): array
    {
        return [
            'discount'       => $this->faker->randomFloat(2, 0, 10), // Máximo 10, de acordo com sua validação
            'delivery_price' => $this->faker->randomFloat(2, 5, 20),
            'user_id'        => User::factory(),
            'client_id'      => Client::factory(),
        ];
    }
}
