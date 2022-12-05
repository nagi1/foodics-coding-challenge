<?php

namespace App\Http\Controllers;

use App\Actions\CalculateMaxProductQuantityAction;
use App\Actions\MakeOrderAction;
use App\Http\Requests\OrderRequest;
use App\Models\Product;
use Arr;

class OrderController extends Controller
{
    public function __invoke(OrderRequest $request)
    {
        $products = Product::query()
            ->with('ingredients')
            ->whereIn('id', Arr::only($request->products, 'product_id'))
            ->get();

        $canMakeEnough = $products->filter()->every(function (Product $product) use (&$request) {
            $quantity = Arr::first($request->products, fn (array $idAndQuantity) => $idAndQuantity['product_id'] === $product->id)['quantity'];

            return app(CalculateMaxProductQuantityAction::class)->execute($product, $quantity) === true;
        });

        if (! $canMakeEnough) {
            // Todo: Send notification to the admin to order more ingredients
            return response()->json([
                'message' => 'Not enough ingredients to make the product',
            ], 422);
        }

        // make order
        $order = app(MakeOrderAction::class)->execute($products, $request->products);
    }
}
