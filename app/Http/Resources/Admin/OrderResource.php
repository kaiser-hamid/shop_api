<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Admin\OrderDetailsResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'order_number' => $this->order_number,
            'customer_name' => $this->user->name,
            'customer_phone' => "0{$this->user->phone}",
            'transaction_number' => $this->transaction_number,

            'items' => OrderDetailsResource::collection($this->orderDetails),

            'subtotal' => $this->subtotal,
            'shipping_cost' => $this->shipping_cost,
            'discount_amount' => $this->discount_amount ?? '0.00',
            'total_amount' => $this->total_amount,
            
            'shipping_address' => "{$this->shipping_address}, {$this->shipping_area}, {$this->shipping_city}.",

            'payment_method' => $this->payment_method,
            'payment_status' => $this->payment_status,
            'order_status' => $this->order_status,
            'order_date' => [
                'date' => $this->created_at->format('d.m.Y'),
                'time' => $this->created_at->format('g:ia'),
            ],

        ];
    }
}
