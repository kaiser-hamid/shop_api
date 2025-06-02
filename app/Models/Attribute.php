<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use App\Traits\HasSortOrder;
class Attribute extends Model
{
    use SoftDeletes, HasSlug, HasSortOrder;
    
    protected $fillable = [
        'name',
        'slug',
        'type',
        'is_required',
        'is_filterable',
        'is_visible'
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnUpdate();
    }

    public function values()
    {
        return $this->hasMany(AttributeValue::class);
    }

    public function productAttributeValues()
    {
        return $this->hasMany(ProductAttributeValue::class);
    }
}
