<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductVariant extends Model
{    
    use SoftDeletes;

    // Auto-generate SKU before creating
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($variant) {
            if (empty($variant->sku)) {
                $variant->sku = $variant->generateSku();
            }
        });
    }

    public function generateSku()
    {
        $product = $this->product;
        $attributes = $this->getAttributeCombination();
        
        // Get category code
        $categoryCode = strtoupper(substr($product->category->name, 0, 3));
        
        // Get product code
        $productCode = strtoupper(preg_replace('/[^A-Z0-9]/', '', $product->name));
        
        // Get attribute codes
        $attributeCodes = [];
        foreach ($attributes as $attribute => $value) {
            $attributeCodes[] = strtoupper(substr($value, 0, 3));
        }
        
        // Combine all parts
        return implode('-', array_merge([$categoryCode, $productCode], $attributeCodes));
    }

    protected $fillable = [
        'product_id',
        'sku',
        'price',
        'sale_price',
        'stock_quantity',
        'status',
        'low_stock_threshold'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'stock_quantity' => 'integer'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function attributeValues()
    {
        return $this->hasMany(ProductVariantAttributeValue::class, 'variant_id');
    }

    public function getAttributeCombination()
    {
        return $this->attributeValues()
            ->with(['attribute', 'attributeValue'])
            ->get()
            ->mapWithKeys(function($item) {
                return [$item->attribute->name => $item->attributeValue->value];
            });
    }

    public function validateAttributeCombination()
    {
        $requiredAttributes = $this->product->attributes()
            ->where('is_required', true)
            ->get();

        $variantAttributes = $this->attributeValues()
            ->pluck('attribute_id')
            ->toArray();

        foreach ($requiredAttributes as $required) {
            if (!in_array($required->id, $variantAttributes)) {
                return false;
            }
        }

        return true;
    }
}
