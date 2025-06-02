<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use App\Traits\HasSortOrder;
class AttributeValue extends Model
{
    use SoftDeletes, HasSlug, HasSortOrder;
    
    protected $fillable = [
        'attribute_id',
        'value',
        'slug',
        'sort_order'
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('value')
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnUpdate();
    }

    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }

    public function productAttributeValues()
    {
        return $this->hasMany(ProductAttributeValue::class);
    }
}
