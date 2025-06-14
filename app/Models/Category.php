<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Category extends Model
{
    use SoftDeletes, HasSlug;  

    protected static function booted()
    {
        static::updated(function ($category) {
            $category->products()->searchable();
        });
    }

    protected $fillable = [
        'name',
        'slug',
        'description',
        'parent_id',
        'level',
        'image',
        'banner',
        'is_active'
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnUpdate();
    }


    /* Relations */
    public function products()
    {
        return $this->belongsToMany(Product::class);
    }

    // Get parent category
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    // Get child categories
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    // Get all ancestors
    public function ancestors()
    {
        return $this->parent()->with('ancestors');
    }

    // Get all descendants
    public function descendants()
    {
        return $this->children()->with('descendants');
    }

    // Get main categories (level 1)
    public static function mainCategories()
    {
        return self::whereNull('parent_id')->get();
    }

    // Get subcategories (level 2)
    public static function subcategories()
    {
        return self::whereNotNull('parent_id')
                   ->whereHas('parent', function($q) {
                       $q->whereNull('parent_id');
                   })
                   ->get();
    }

    // Get sub-subcategories (level 3)
    public static function subSubcategories()
    {
        return self::whereNotNull('parent_id')
                   ->whereHas('parent', function($q) {
                       $q->whereNotNull('parent_id');
                   })
                   ->get();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }
}
