<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Make Burger
        app(MakeProductAction::class)->execute([
            'name' => 'Burger',
            'description' => 'Beef Burger',
            'price' => 35.99,
        ], [
            [
                'ingredient_id' => 1, // Beef
                'quantity' => 1,
                'weight' => 0.150, // 150g
            ],
            [
                'ingredient_id' => 2, // Cheese
                'quantity' => 1,
                'weight' => 0.030, // 30g
            ],
            [
                'ingredient_id' => 3, // Onion
                'quantity' => 1,
                'weight' => 0.020, // 20g
            ],
        ]);
    }
}
