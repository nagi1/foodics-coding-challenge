<?php

namespace App\Actions;

use App\Models\Product;

class MakeProductAction
{
    public function execute(array $data, array $ingredients): Product
    {
        $product = Product::create($data);

        foreach ($ingredients as $ingredient) {
            $product->ingredients()->attach($ingredient['ingredient_id'], [
                'weight' => $ingredient['weight'],
                'quantity' => $ingredient['quantity'],
            ]);
        }

        return $product;
    }
}
