<?php

namespace App\Actions;

use App\Models\Ingredient;
use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;

class MakeOrderAction
{
    public function execute(Collection &$products, array $productsIdsAndQuantities): Order
    {
        // calculate total price and quantity
        $totalPrice = 0;

        foreach ($productsIdsAndQuantities as $productIdAndQuantity) {
            $totalPrice += $products->find($productIdAndQuantity['product_id'])->price * $productIdAndQuantity['quantity'];
        }

        // create order
        $order = Order::create([
            'user_id' => auth()->id(),
            'total_price' => $totalPrice,
        ]);

        // attach products to order
        foreach ($productsIdsAndQuantities as $productIdAndQuantity) {
            $order->products()->attach($productIdAndQuantity['product_id'], [
                'quantity' => $productIdAndQuantity['quantity'],
            ]);
        }

        //  update ingredients quantities
        foreach ($productsIdsAndQuantities as $productIdAndQuantity) {
            $product = $products->find($productIdAndQuantity['product_id']);
            $product->ingredients->each(function (Ingredient $ingredient) use ($productIdAndQuantity) {
                $ingredient->quantity -= $ingredient->pivot->weight * $productIdAndQuantity['quantity'];
                $ingredient->save();
            });
        }

        return $order;
    }
}
