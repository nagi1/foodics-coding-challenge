<?php

namespace App\Actions;

use App\Enums\IngredientUnit;
use App\Models\Ingredient;
use App\Models\Product;

class CanMakeProductBasedOnQuantityAction
{
    public function execute(Product &$product, int $quantity): bool
    {
        // Burger has 2 ingredients (Beef and Cheese), so we need to calculate the max quantity
        // We have 20kg of Beef and 5kg of Cheese, so how many burgers we can make?
        // to make a burger we need 150g of Beef and 0.030g of Cheese
        // that we can make with the current ingredients stock
        // note that we are using the weight of the ingredient in grams
        // 5000g / 0.030g = 33 burgers
        // 20000g / 150g = 133 burgers
        // so we can make 33 burgers
        $maxQuantity = $product->ingredients->min(function (Ingredient $ingredient) {
            $weightInProduct = app(CalculateUsedStockAction::class)->execute($ingredient->pivot, 1);
            $weightInProduct = $weightInProduct <= 0 ? 1 : $weightInProduct;

            return floor(IngredientUnit::tryFrom($ingredient->unit)->toGrams($ingredient->stock) / $weightInProduct);
        });

        return $maxQuantity >= $quantity;
    }
}
