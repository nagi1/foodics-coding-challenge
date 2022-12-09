<?php
namespace App\Actions;

use App\Models\IngredientProduct;
use App\Enums\IngredientUnit;

class CalculateUsedStockAction
{
    public function execute(IngredientProduct $ingredientProduct, int $requiredQuantity = 0): int
    {
        return IngredientUnit::tryFrom($ingredientProduct->unit)->toGrams($ingredientProduct->weight) *
                $ingredientProduct->quantity *
                $requiredQuantity;
    }
}
