<?php

namespace App\Services;

use App\Actions\CanMakeProductBasedOnQuantityAction;
use App\Enums\OrderStatus;
use App\Events\OrderCreated;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;

class OrderService
{
    protected Collection $products;

    public function __construct(protected array $productsIdsAndQuantities)
    {
    }

    public function createOrder(array $attributes = []): OrderStatus|Order
    {
        $this->products = Product::query()
            ->with('ingredients')
            ->whereIn('id', collect($this->productsIdsAndQuantities)->pluck('product_id')->toArray())
            ->get();

        if (! $this->canMakeEnough()) {
            return OrderStatus::NOT_ENOUGH_INGREDIENTS;
        }

        // calculate total price and quantity
        $totalPrice = $this->calculateTotalPrice();

        $order = $this->makeOrder([
            'user_id' => auth()->id(),
            'total_price' => $totalPrice,
        ] + $attributes);

        // After creating the order, we attach the products to it
        collect($this->productsIdsAndQuantities)->each(function (array $idAndQuantity) use (&$order) {
            $order->products()->attach($idAndQuantity['product_id'], [
                'quantity' => $idAndQuantity['quantity'],
            ]);
        });

        // After the order is created, we fire the OrderCreated event
        // that will update the ingredient's stock and notify the
        // admin if the stock is bellow the threshold percentage
        event(new OrderCreated($order));

        return $order;
    }

    protected function makeOrder(array $data): Order
    {
        return Order::create($data);
    }

    protected function calculateTotalPrice(): float
    {
        return collect($this->productsIdsAndQuantities)->sum(fn (array $idAndQuantity) => $this->products->find($idAndQuantity['product_id'])->price * $idAndQuantity['quantity']);
    }

    protected function canMakeEnough(): bool
    {
        // Check if we can make all the products based on the quantity
        // every() will return false if one of the products is false
        // If we can't make one of the products, we return false
        return $this->products->filter()->every(function (Product $product) {
            $quantity = Arr::first($this->productsIdsAndQuantities, fn (array $idAndQuantity) => $idAndQuantity['product_id'] === $product->id)['quantity'];

            return app(CanMakeProductBasedOnQuantityAction::class)->execute($product, $quantity) === true;
        });
    }
}
