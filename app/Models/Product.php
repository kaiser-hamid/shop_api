<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;
use App\Enums\ProductStatusEnum;
use App\Enums\ProductVariantStatusEnum;
use Spatie\Sluggable\SlugOptions;
use Spatie\Sluggable\HasSlug;
use App\Traits\HasFileUpload;
use App\Models\Scopes\LatestScope;
use App\Enums\StatusEnum;
use Illuminate\Support\Facades\Auth;

class Product extends Model
{
    use SoftDeletes, Searchable, HasSlug, HasFileUpload;

    protected static function booted()
    {
        static::addGlobalScope(new LatestScope);
    }
    
    protected $fillable = [
        'name',
        'slug',
        'size',
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

    public function toSearchableArray()
    {
        // Load categories with their parent relationships
        $this->load(['categories.parent', 'categories.parent.parent']);
        
        // Build categories with full hierarchical paths
        $categories = $this->buildCategoryPaths();
        $brand = $this->brand?->name ?? null;
        $variant = $this->variants?->first();

        // Build hierarchical categories for Algolia
        $hierarchicalCategories = $this->buildHierarchicalCategories();
        
        //TODO: Add rating
        $rating = rand(1, 5);
        
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'size' => $this->size,
            'brand' => $brand,
            'categories' => $categories,
            'hierarchicalCategories' => $hierarchicalCategories,
            'price' => $variant?->price ?? null,
            'sale_price' => $variant?->sale_price ?? null,
            'discount_percentage' => $variant?->discount_percentage ?? null,
            'stock_quantity' => $variant?->stock_quantity ?? null,
            'rating' => $rating,
            'status' => $this->status,
            'featured_image' => asset('storage/' . $this->featured_image),
        ];
    }

    /**
     * Build categories array with full hierarchical paths
     * 
     * @return array
     */
    private function buildCategoryPaths(): array
    {
        $categoryPaths = [];
        
        foreach ($this->categories as $category) {
            $categoryPath = $this->getCategoryHierarchyPath($category);
            
            // For each category, generate paths for all levels up to its depth
            if (count($categoryPath) >= 1) {
                // Level 0: Root category
                $categoryPaths[] = $categoryPath[0]->name;
                
                if (count($categoryPath) >= 2) {
                    // Level 1: Root > Subcategory
                    $categoryPaths[] = $categoryPath[0]->name . ' > ' . $categoryPath[1]->name;
                    
                    if (count($categoryPath) >= 3) {
                        // Level 2: Root > Subcategory > Sub-subcategory
                        $categoryPaths[] = $categoryPath[0]->name . ' > ' . $categoryPath[1]->name . ' > ' . $categoryPath[2]->name;
                    }
                }
            }
        }
        
        return array_values(array_unique($categoryPaths));
    }

    /**
     * Build hierarchical categories structure for Algolia
     * 
     * @return array
     */
    private function buildHierarchicalCategories(): array
    {
        $hierarchicalCategories = [
            'lvl0' => [],
            'lvl1' => [],
            'lvl2' => []
        ];

        foreach ($this->categories as $category) {
            $categoryPath = $this->getCategoryHierarchyPath($category);
            
            // For each category, generate entries for all levels up to its depth
            if (count($categoryPath) >= 1) {
                // Level 0: Root category
                $hierarchicalCategories['lvl0'][] = $categoryPath[0]->name;
                
                if (count($categoryPath) >= 2) {
                    // Level 1: Root > Subcategory
                    $hierarchicalCategories['lvl1'][] = $categoryPath[0]->name . ' > ' . $categoryPath[1]->name;
                    
                    if (count($categoryPath) >= 3) {
                        // Level 2: Root > Subcategory > Sub-subcategory
                        $hierarchicalCategories['lvl2'][] = $categoryPath[0]->name . ' > ' . $categoryPath[1]->name . ' > ' . $categoryPath[2]->name;
                    }
                }
            }
        }

        // Remove duplicates and ensure unique values
        $hierarchicalCategories['lvl0'] = array_values(array_unique($hierarchicalCategories['lvl0']));
        $hierarchicalCategories['lvl1'] = array_values(array_unique($hierarchicalCategories['lvl1']));
        $hierarchicalCategories['lvl2'] = array_values(array_unique($hierarchicalCategories['lvl2']));

        return $hierarchicalCategories;
    }

    /**
     * Get the full category path for a given category
     * 
     * @param Category $category
     * @return array
     */
    private function getCategoryHierarchyPath($category): array
    {
        $path = [];
        $currentCategory = $category;

        // Build path from current category up to root
        while ($currentCategory) {
            array_unshift($path, $currentCategory);
            $currentCategory = $currentCategory->parent;
        }

        return $path;
    }

    public function scopeActive($query)
    {
        return $query->where('status', ProductStatusEnum::PUBLISHED);
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

    public function ingredient()
    {
        return $this->hasOne(ProductIngredient::class);
    }

    public function usage()
    {
        return $this->hasOne(ProductUsage::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    
    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function topVariant()
    {
        return $this->hasOne(ProductVariant::class)->where('status', StatusEnum::ACTIVE);
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
        $userId = Auth::id();
        if (!$userId) {
            return collect();
        }
        
        $recentViews = ProductView::where('user_id', $userId)
            ->where('viewed_at', '>', now()->subDays(7))
            ->where('product_id', '!=', $this->id)
            ->orderBy('viewed_at', 'desc')
            ->limit($limit)
            ->get();

        return $recentViews->pluck('product');
    }


}
