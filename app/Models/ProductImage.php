<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasFileUpload;
use App\Traits\HasSortOrder;

class ProductImage extends Model
{
    use HasFileUpload, HasSortOrder;

    protected $fillable = ['product_id', 'image_path', 'sort_order'];

    protected array $fileColumns = ['image_path'];
    protected string $fileDirectory = 'products';

    protected static function getSortOrderConditions($model)
    {
        return [
            'product_id' => $model->product_id
        ];
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}