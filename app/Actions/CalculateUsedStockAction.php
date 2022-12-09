<?php

namespace App\Actions;

use App\Enums\IngredientUnit;
use App\Models\IngredientProduct;

class CalculateUsedStockAction
{
    public function execute(IngredientProduct $ingredientProduct, int $requiredQuantity = 0): int
    {
        return IngredientUnit::tryFrom($ingredientProduct->unit)->toGrams($ingredientProduct->weight) *
                $ingredientProduct->quantity *
                $requiredQuantity;
    }
}
