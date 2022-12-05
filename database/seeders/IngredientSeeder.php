<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
                'name' => 'Beef',
                'description' => 'Cow Beef',
                'stock' => 20,
                'max_stock' => 20,
                'unit' => 'kg'
            ],
            [
                'name' => 'Cheese',
                'description' => 'Swizz Cheese',
                'stock' => 5,
                'max_stock' => 5,
                'unit' => 'kg'
            ],
            [
                'name' => 'Onion',
                'description' => 'White Onion',
                'stock' => 1,
                'max_stock' => 1,
                'unit' => 'kg'
            ],
        ];

        foreach ($ingredients as $ingredient) {
            \App\Models\Ingredient::create($ingredient);
        }
    }
}
