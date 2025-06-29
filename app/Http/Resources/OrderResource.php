<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
            //Order Details
            'order_number' => $this->order_number,
            'order_status' => $this->order_status,
            'order_details' => OrderDetailsResource::collection($this->orderDetails),

            //Customer & Order Details
            'customer_name' => $this->user->name,
            'customer_phone' => "0{$this->user->phone}",
            'customer_email' => $this->user->email ?? 'N/A',
            'delivery_type' => $this->is_inside_dhaka ? 'Inside Dhaka' : 'Outside Dhaka',
            'payment_method' => $this->payment_method === 'cod' ? 'Cash on Delivery' : $this->payment_method,

            //Address
            'city' => $this->shipping_city,
            'address' => $this->shipping_address,
            'area' => $this->shipping_area,

            //Order summary
            'order_date' => $this->created_at->format('M j, Y'),
            'order_time' => $this->created_at->format('g:i A'),
            'subtotal' => $this->subtotal,
            'delivery_fee' => $this->shipping_cost,
            'order_total' => $this->total_amount,
        ];
    }
}
