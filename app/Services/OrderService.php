<?php

namespace App\Services;

use App\Actions\CalculateMaxProductQuantityAction;
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

        // attach products to order
        collect($this->productsIdsAndQuantities)->each(function (array $idAndQuantity) use (&$order) {
            $order->products()->attach($idAndQuantity['product_id'], [
                'quantity' => $idAndQuantity['quantity'],
            ]);
        });

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
        return $this->products->filter()->every(function (Product $product) {
            $quantity = Arr::first($this->productsIdsAndQuantities, fn (array $idAndQuantity) => $idAndQuantity['product_id'] === $product->id)['quantity'];

            return app(CalculateMaxProductQuantityAction::class)->execute($product, $quantity) === true;
        });
    }
}
