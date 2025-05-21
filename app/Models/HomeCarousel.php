<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class HomeCarousel extends Model
{   
    use SoftDeletes;
    
    protected $fillable = [
        'slug',
        'carousel_image',
        'alt',
        'is_active',
        'sort_order',
    ];
}
