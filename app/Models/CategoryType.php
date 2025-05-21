<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class CategoryType extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'name',
        'slug',
        'description'
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_category_types')->withTimestamps();
    }
}
