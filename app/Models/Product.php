<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\ProductStatusEnum;
use Spatie\Sluggable\SlugOptions;
use Spatie\Sluggable\HasSlug;
use App\Traits\HasFileUpload;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\ProductHistory;
use App\Models\ProductView;
use App\Models\Brand;

class Product extends Model
{
    use SoftDeletes, HasSlug, HasFileUpload;
    
    protected $fillable = [
        'name',
        'slug',
        'description',
        'brand_id',
        'weight',
        'dimensions',
        'featured_image',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'status',
    ];

    protected array $fileColumns = ['featured_image'];
    protected string $fileDirectory = 'products';
    protected bool $hasThumbnail = true;

    protected $casts = [
        'meta_keywords' => 'array',
        'status' => ProductStatusEnum::class,
    ];

    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnUpdate();
    }

    // Relationships
    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function productImages()
    {
        return $this->hasMany(ProductImage::class);
    }


    /* Relation end */

     // Get main categories
    public function mainCategories()
    {
        return $this->belongsToMany(Category::class)
            ->whereNull('parent_id')
            ->withTimestamps();
    }

    // Get subcategories
    public function subcategories()
    {
        return $this->belongsToMany(Category::class)
            ->whereNotNull('parent_id')
            ->whereHas('parent', function($q) {
                $q->whereNull('parent_id');
            })
            ->withTimestamps();
    }

    // Get sub-subcategories
    public function subSubcategories()
    {
        return $this->belongsToMany(Category::class)
            ->whereNotNull('parent_id')
            ->whereHas('parent', function($q) {
                $q->whereNotNull('parent_id');
            })
            ->withTimestamps();
    }

    // Helper method to get the deepest category
    public function getDeepestCategory()
    {
        if ($this->sub_subcategory_id) {
            return $this->subSubcategory;
        }
        if ($this->subcategory_id) {
            return $this->subcategory;
        }
        return $this->category;
    }

    // Helper method to get full category path
    public function getCategoryPath()
    {
        $path = [];
        
        if ($this->category) {
            $path[] = $this->category;
        }
        if ($this->subcategory) {
            $path[] = $this->subcategory;
        }
        if ($this->subSubcategory) {
            $path[] = $this->subSubcategory;
        }
        
        return $path;
    }

     public function validateCategoryStructure()
    {
        // If subcategory is set, category must be set
        if ($this->subcategory_id && !$this->category_id) {
            return false;
        }

        // If sub-subcategory is set, subcategory must be set
        if ($this->sub_subcategory_id && !$this->subcategory_id) {
            return false;
        }

        // Validate category hierarchy
        if ($this->subcategory_id) {
            $subcategory = Category::find($this->subcategory_id);
            if ($subcategory->parent_id !== $this->category_id) {
                return false;
            }
        }

        if ($this->sub_subcategory_id) {
            $subSubcategory = Category::find($this->sub_subcategory_id);
            if ($subSubcategory->parent_id !== $this->subcategory_id) {
                return false;
            }
        }

        return true;
    }

    public function history()
    {
        return $this->hasMany(ProductHistory::class);
    }

    // Helper Methods
    public function isOnSale()
    {
        return $this->sale_price !== null && 
               $this->sale_start_date <= now() && 
               $this->sale_end_date >= now();
    }

    public function getCurrentPrice()
    {
        return $this->isOnSale() ? $this->sale_price : $this->regular_price;
    }

    public function isLowStock()
    {
        return $this->stock_quantity <= $this->low_stock_threshold;
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function getVariantsWithAttributes()
    {
        return $this->variants()
            ->with(['attributeValues.attribute', 'attributeValues.attributeValue'])
            ->get()
            ->map(function($variant) {
                return [
                    'id' => $variant->id,
                    'sku' => $variant->sku,
                    'price' => $variant->price,
                    'sale_price' => $variant->sale_price,
                    'stock_quantity' => $variant->stock_quantity,
                    'attributes' => $variant->getAttributeCombination()
                ];
            });
    }

     public function getRecommendedProducts($limit = 5)
    {
        return Product::where(function($query) {
            // Same category
            $query->where('category_id', $this->category_id)
                  ->orWhere('subcategory_id', $this->subcategory_id)
                  ->orWhere('sub_subcategory_id', $this->sub_subcategory_id)
                  // Same brand
                  ->orWhere('brand_id', $this->brand_id)
                  // Similar price range (±20%)
                  ->orWhereBetween('regular_price', [
                      $this->regular_price * 0.8,
                      $this->regular_price * 1.2
                  ]);
        })
        ->where('id', '!=', $this->id)
        ->inRandomOrder()
        ->limit($limit)
        ->get();
    }

    public function getAlsoViewedProducts($limit = 5)
    {
        // Get recent views from the same user
        $recentViews = ProductView::where('user_id', auth()->id())
            ->where('viewed_at', '>', now()->subDays(7))
            ->where('product_id', '!=', $this->id)
            ->orderBy('viewed_at', 'desc')
            ->limit($limit)
            ->get();

        return $recentViews->pluck('product');
    }


}
