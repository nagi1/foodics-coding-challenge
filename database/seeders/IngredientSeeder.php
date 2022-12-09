<?php

namespace Database\Seeders;

use App\Models\Ingredient;
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
        Ingredient::factory()->makeBeef(20_000)->create();
        Ingredient::factory()->makeCheese(5000)->create();
        Ingredient::factory()->makeOnion(1000)->create();
    }
}
