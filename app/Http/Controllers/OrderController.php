<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Http\Requests\OrderRequest;
use App\Services\OrderService;

class OrderController extends Controller
{
    public function __invoke(OrderRequest $request)
    {
        $order = app(OrderService::class, ['productsIdsAndQuantities' => $request->products])->createOrder();

        if ($order === OrderStatus::NOT_ENOUGH_INGREDIENTS) {
            return response()->json([
                'message' => 'We faced a problem while creating your order, please try again later...',
                'data' => [],
            ], 422);
        }

        return response()->json([
            'message' => 'Order created successfully',
            'data' => [
                'order' => [
                    'id' => $order->id,
                    'total_price' => $order->total_price,
                ],
            ],
        ], 201);
    }
}
