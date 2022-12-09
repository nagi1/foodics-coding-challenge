<?php

namespace App\Actions;

use App\Enums\IngredientUnit;
use App\Models\Ingredient;
use App\Models\Product;

class CanMakeProductBasedOnQuantityAction
{
    public function execute(Product &$product, int $quantity): bool
    {
        // First calculate how many of the product can we make with the
        // current stock of each ingredient. Then we take the minimum
        // of all the results and compare with the given quantity.
        $maxQuantity = $product->ingredients->min(function (Ingredient $ingredient) {
            $weightInProduct = app(CalculateUsedStockAction::class)->execute($ingredient->pivot, 1);
            $weightInProduct = $weightInProduct <= 0 ? 1 : $weightInProduct;

            return floor(IngredientUnit::tryFrom($ingredient->unit)->toGrams($ingredient->stock) / $weightInProduct);
        });

        return $maxQuantity >= $quantity;
    }
}
