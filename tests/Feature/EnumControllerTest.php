<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnumControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_requires_authentication(): void
    {
        $response = $this->getJson(route('enums.index'));
        $response->assertStatus(401);
    }

    public function test_it_returns_enums_data_correctly(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson(route('enums.index'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'categories' => [
                '*' => ['value', 'name']
            ],
            'payment_statuses' => [
                '*' => ['value', 'name']
            ],
            'payment_types' => [
                '*' => ['value', 'name']
            ],
            'statuses' => [
                '*' => ['value', 'name']
            ],
        ]);
    }
}
