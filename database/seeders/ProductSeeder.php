<?php

namespace Database\Seeders;

use App\Actions\MakeProductAction;
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
                'weight' => 150,
            ],
            [
                'ingredient_id' => 2, // Cheese
                'quantity' => 1,
                'weight' => 30,
            ],
            [
                'ingredient_id' => 3, // Onion
                'quantity' => 1,
                'weight' => 20,
            ],
        ]);
    }
}
