<?php

namespace Database\Seeders;

use App\Enums\IngredientUnit;
use Illuminate\Database\Seeder;

class IngredientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $ingredients = [
            [
                'id' => 1,
                'name' => 'Beef',
                'description' => 'Cow Beef',
                'stock' => 20_000,
                'max_stock' => 20_000,
                'unit' => IngredientUnit::Grams->value,
            ],
            [
                'id' => 2,
                'name' => 'Cheese',
                'description' => 'Swizz Cheese',
                'stock' => 5000,
                'max_stock' => 5000,
                'unit' => IngredientUnit::Grams->value,
            ],
            [
                'id' => 3,
                'name' => 'Onion',
                'description' => 'White Onion',
                'stock' => 1000,
                'max_stock' => 1000,
                'unit' => IngredientUnit::Grams->value,
            ],
        ];

        foreach ($ingredients as $ingredient) {
            \App\Models\Ingredient::create($ingredient);
        }
    }
}
