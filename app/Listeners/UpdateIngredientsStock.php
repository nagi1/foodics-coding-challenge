<?php

namespace App\Listeners;

use App\Actions\NotifyStockBellowThresholdAction;
use App\Enums\IngredientUnit;
use App\Events\OrderCreated;
use App\Models\Ingredient;
use App\Models\Product;

class UpdateIngredientsStock
{
    public function handle(OrderCreated $event)
    {
        $order = $event->order->load('products.ingredients');

        $bellowThreshold = collect([]);

        $order->products->each(function (Product $product) use (&$bellowThreshold) {
            $product->ingredients->each(function (Ingredient $ingredient) use ($product, &$bellowThreshold) {
                $ingredient->stock -= IngredientUnit::tryFrom($ingredient->pivot->unit)->toGrams($ingredient->pivot->weight) * $product->pivot->quantity;
                $ingredient->update();

                if ($ingredient->stockBellowPercentageThreshold(config('app.stock_percentage_threshold', 50))) {
                    $bellowThreshold->push($ingredient);
                }
            });
        });

        if ($bellowThreshold->isNotEmpty()) {
            app(NotifyStockBellowThresholdAction::class)->execute($bellowThreshold);
        }
    }
}
