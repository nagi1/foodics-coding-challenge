<?php

namespace App\Listeners;

use App\Actions\CalculateUsedStockAction;
use App\Actions\NotifyStockBellowThresholdAction;
use App\Events\OrderCreated;
use App\Models\Ingredient;
use App\Models\Product;
use DB;

class UpdateIngredientsStock
{
    public function handle(OrderCreated $event)
    {
        DB::beginTransaction();

        $order = $event->order->load('products.ingredients');

        $bellowThreshold = collect([]);

        // For each product in the order we need to update the
        // stock of each ingredient used in the product, based
        // on the quantity of the product in the order.
        $order->products->each(function (Product $product) use (&$bellowThreshold) {
            $product->ingredients->each(function (Ingredient $ingredient) use ($product, &$bellowThreshold) {
                $ingredient->stock -= app(CalculateUsedStockAction::class)->execute($ingredient->pivot, $product->pivot->quantity);
                $ingredient->update();

                // If the stock is bellow the threshold percentage push the
                // ingredient to the bellowThreshold collection so we can
                // notify the user later.
                if ($ingredient->stockBellowPercentageThreshold(config('app.stock_percentage_threshold', 50))) {
                    $bellowThreshold->push($ingredient);
                }
            });
        });

        DB::commit();

        if ($bellowThreshold->isNotEmpty()) {
            app(NotifyStockBellowThresholdAction::class)->execute($bellowThreshold);
        }
    }
}
