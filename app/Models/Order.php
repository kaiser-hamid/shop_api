<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            $order->order_number = self::generateOrderNumber();
        });
    }

    private static function generateOrderNumber()
    {
        $date = now()->format('ymd');
        $lastOrder = self::whereDate('created_at', today())->latest()->first();
        
        $sequence = $lastOrder ? (int)substr($lastOrder->order_number, -4) + 1 : 1;
        
        return $date . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }


    protected $fillable = [
        'user_id',
        'shipping_address_id',
        'billing_address_id',
        'subtotal',
        'tax_amount',
        'shipping_cost',
        'discount_amount',
        'total_amount',
        'transaction_number',
        'payment_method',
        'payment_status',
        'order_status',
        'is_inside_dhaka',
        'notes',
    ];


    /* Relationships */
    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
