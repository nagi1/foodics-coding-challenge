<?php

namespace App\Actions;

use App\Enums\IngredientUnit;
use App\Models\IngredientProduct;

class CalculateUsedStockAction
{
    public function execute(IngredientProduct $ingredientProduct, int $requiredQuantity = 0): int
    {
        // Because products can be made from multiple ingredients,
        // we need to calculate how much of each ingredient is used
        // in the product. We do this by multiplying the weight of
        // the ingredient in the product by the quantity required.
        return IngredientUnit::tryFrom($ingredientProduct->unit)->toGrams($ingredientProduct->weight) *
                $ingredientProduct->quantity *
                $requiredQuantity;
    }
}
