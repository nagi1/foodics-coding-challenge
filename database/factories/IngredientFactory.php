<?php

namespace Database\Factories;

use App\Enums\IngredientUnit;
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
            'unit' => $this->faker->randomElement(['g', 'kg']),
            'notified_at' => null,
        ];
    }

    public function makeBeef(int $stock, IngredientUnit $unit = IngredientUnit::Grams): self
    {
        return $this->state([
            'name' => 'Beef',
            'description' => 'Cow Beef',
            'stock' => $stock,
            'max_stock' => $stock,
            'unit' => $unit->value,
        ]);
    }

    public function makeChicken(int $stock, IngredientUnit $unit = IngredientUnit::Grams): self
    {
        return $this->state([
            'name' => 'Chicken',
            'description' => 'Chicken Meat',
            'stock' => $stock,
            'max_stock' => $stock,
            'unit' => $unit->value,
        ]);
    }

    public function makeTomato(int $stock, IngredientUnit $unit = IngredientUnit::Grams): self
    {
        return $this->state([
            'name' => 'Tomato',
            'description' => 'Red Tomato',
            'stock' => $stock,
            'max_stock' => $stock,
            'unit' => $unit->value,
        ]);
    }

    public function makeCheese(int $stock, IngredientUnit $unit = IngredientUnit::Grams): self
    {
        return $this->state([
            'name' => 'Cheese',
            'description' => 'Swizz Cheese',
            'stock' => $stock,
            'max_stock' => $stock,
            'unit' => $unit->value,
        ]);
    }

    public function makeOnion(int $stock, IngredientUnit $unit = IngredientUnit::Grams): self
    {
        return $this->state([
            'name' => 'Onion',
            'description' => 'White Onion',
            'stock' => $stock,
            'max_stock' => $stock,
            'unit' => $unit->value,
        ]);
    }
}
