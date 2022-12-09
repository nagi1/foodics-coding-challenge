<?php

namespace App\Actions;

use App\Models\Ingredient;
use App\Models\Product;

class MakeProductUsingIngredientsAction
{
    public function execute(array $data, array $ingredients): Product
    {
        $product = Product::create($data);

        foreach ($ingredients as $ingredient) {
            $ingredientId = $ingredient['ingredient_id'] instanceof Ingredient
                ? $ingredient['ingredient_id']->id
                : $ingredient['ingredient_id'];

            $product->ingredients()->attach($ingredientId, [
                'weight' => $ingredient['weight'],
                'quantity' => $ingredient['quantity'],
            ]);
        }

        return $product;
    }
}
