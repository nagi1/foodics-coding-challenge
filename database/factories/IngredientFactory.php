<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ingredient>
 */
class IngredientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'description' => $this->faker->text,
            'stock' => $this->faker->numberBetween(0, 100),
            'max_stock' => $this->faker->numberBetween(0, 100),
            'unit' => $this->faker->randomElement(['kg']),
        ];
    }
}
