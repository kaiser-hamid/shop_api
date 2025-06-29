<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\OrderService;
use App\Http\Resources\Admin\OrderResource;

class OrderController extends Controller
{
    public function __construct(private OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function index()
    {
        $orders = $this->orderService->getOrdersPaginated();

        if ($orders->isEmpty()) {
            return $this->errorResponse(message: 'No orders found.');
        }
        
        $data = [
            'data' => OrderResource::collection($orders->items()),
            'per_page' => $orders->perPage(),
            'total' => $orders->total()
        ];

        return $this->successResponse(message: 'Success! Orders fetched.', data: $data);
    }
}
