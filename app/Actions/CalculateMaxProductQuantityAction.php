<?php

namespace App\Actions;

use App\Models\Ingredient;
use App\Models\Product;

class CalculateMaxProductQuantityAction
{
    public function execute(Product &$product, int $quantity): bool
    {
        $maxQuantity = $quantity;

        $product->ingredients->each(function (Ingredient $ingredient) use (&$maxQuantity) {
            $maxQuantity = min($maxQuantity, $ingredient->pivot->quantity / $ingredient->pivot->weight);
        });

        return $maxQuantity >= $quantity;
    }
}
