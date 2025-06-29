<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckoutRequest;
use App\Http\Resources\OrderResource;
use App\Services\OrderService;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function orderSave(CheckoutRequest $request, OrderService $orderService)
    {
        $user = $orderService->getUserByPhone($request->validated());

        $order = $orderService->createOrder($request->validated(), $user);

        if(!$order) {
            return $this->errorResponse(message: 'Failed to create order');
        }

        return $this->successResponse(message: 'Order created successfully', data: ['order_id' => $order->id]);
    }

    public function orderDetails(OrderService $orderService, $order_id)
    {
        $order = $orderService->getOrderDetails($order_id);

        return $this->successResponse(message: 'Order details fetched successfully', data: new OrderResource($order));
    }
}
