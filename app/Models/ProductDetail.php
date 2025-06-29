<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductDetail extends Model
{
    /* Fillable */
    protected $fillable = [
        'product_id',
        'variant_id',
        'quantity',
        'price',
    ];
}
