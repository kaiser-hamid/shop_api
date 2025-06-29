<?php

namespace App\Services;

use App\Enums\AddressTypeEnum;
use App\Enums\OrderStatusEnum;
use App\Models\Address;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;

class OrderService
{
    /*======================================== Frontend ========================================*/

    public function createOrder($data, $user)
    {
        try {
            DB::beginTransaction();

            $productDetails = $this->getProductDetailsArray($data); // [product_id, variant_id, unit_price, quantity]

            $order = new Order;
            $order->user_id = $user->id;

            $order->subtotal = $subtotal = $this->calculateSubtotal($productDetails);
            $order->shipping_cost = $shippingCost = $this->calculateShippingCost($data);
            $order->total_amount = $subtotal + $shippingCost;

            if ($data['customer_city'] != 'Dhaka') {
                $order->transaction_number = $data['transaction_number'];
            } 
            
            $order->is_inside_dhaka =  $data['customer_city'] == 'Dhaka';
            $order->shipping_city = $data['customer_city'];
            $order->shipping_area = $data['customer_area'];
            $order->shipping_address = $data['customer_address'];

            $order->save();

            $order->orderDetails()->saveMany($this->createOrderDetails($productDetails));

            DB::commit();

            return $order;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new HttpResponseException(response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'data' => null,
                'errors' => $e->getTrace(),
            ], 500));
        }
    }

    public function createOrderDetails($productDetails)
    {
        $orderDetails = [];
        foreach ($productDetails as $detail) {
            $orderDetails[] = new OrderDetail([
                'product_id' => $detail['product_id'],
                'variant_id' => $detail['variant_id'],
                'price' => $detail['unit_price'],
                'quantity' => $detail['quantity'],
                'total_price' => ($detail['unit_price'] * $detail['quantity']),
            ]);
        }

        return $orderDetails;
    }

    public function getProductDetailsArray($data) 
    {
        $productDetails = [];
        foreach ($data['item_slugs'] as $key => $slug) {
            $quantity = $data['item_quantities'][$key];
            $product = Product::with('topVariant')
            ->whereHas('topVariant')
            ->where('slug', $slug)
            ->firstOrFail();

            if ($product->topVariant->stock_quantity < $quantity) {
                throw new \Exception('Product out of stock');
            }

            // Update stock quantity
            $product->topVariant->stock_quantity -= $quantity;
            $product->topVariant->save();

            $productDetails[] = [
                'product_id' => $product->id,
                'variant_id' => $product->topVariant->id,
                'unit_price' => ceil($product->topVariant->sale_price),
                'quantity' => $quantity,
            ];
        }

        return $productDetails;
    }

    public function calculateSubtotal($productDetails)
    {
        $subtotal = 0;
        foreach ($productDetails as $detail) {
            $subtotal += ($detail['unit_price'] * $detail['quantity']);
        }

        return $subtotal;
    }

    public function calculateShippingCost($data)
    {
        $shippingCost = config('site.shipping_cost');

        return $data['customer_city'] == 'Dhaka' ? $shippingCost['inside_dhaka'] : $shippingCost['outside_dhaka'];
    }

    public function getUserByPhone($data)
    {
        $phone = preg_replace('/^(\+880|880|0)?/', '', $data['customer_phone']);
        $user = User::where('phone', $phone)->firstOrNew();

        if (!$user->exists) {
            $user->name = $data['customer_name'];
            $user->phone = $phone;
            $user->save();
            $user->addresses()->save(new Address([
                'city' => $data['customer_city'],
                'area' => $data['customer_area'],
                'address_line' => $data['customer_address'],
                'type' => AddressTypeEnum::DEFAULT,
            ]));
        }

        return $user;
    }

    public function getOrderDetails($order_id)
    {
        return Order::with(['orderDetails.product', 'user'])->whereHas('orderDetails')->findOrFail($order_id);
    }

    /*======================================== Admin ========================================*/

    /**
     * Get orders paginated
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getOrdersPaginated()
    {
        $search = request()->query('search');
        return Order::with(['user:id,name,phone', 'orderDetails.product:id,name,slug,featured_image'])->where(function($query) use ($search) {
            if($search) {
                $query->where('order_number', 'like', '%' . $search . '%');
            }
        })->paginate(20);
    }
}