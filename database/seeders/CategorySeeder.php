<?php

namespace Database\Seeders;

use App\Enums\CategoryEnum;
use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Populate using CategoryEnum values
        foreach (CategoryEnum::cases() as $categoryEnum) {
            $name = $categoryEnum->value;
            
            // Example custom profit margins based on category name
            $margin = 30; // default 30%
            if (str_contains($name, 'Básicos')) {
                $margin = 15;
            } elseif (str_contains($name, 'Higiene') || str_contains($name, 'Limpeza')) {
                $margin = 40;
            } elseif (str_contains($name, 'Pet')) {
                $margin = 50;
            }
            
            Category::firstOrCreate(
                ['name' => $name],
                ['margem_lucro_padrao' => $margin]
            );
        }
    }
}
