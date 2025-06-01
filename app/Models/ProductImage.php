<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasFileUpload;
class ProductImage extends Model
{
    use HasFileUpload;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $maxOrder = static::where('product_id', $model->product_id)
                ->max('sort_order') ?? 0;
            $model->sort_order = $maxOrder + 1;
        });
    }

    protected $fillable = ['product_id', 'image_path', 'sort_order'];

    protected array $fileColumns = ['image_path'];
    protected string $fileDirectory = 'products';

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}