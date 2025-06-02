<?php

namespace App\Traits;

trait HasSortOrder
{
    /**
     * Get the conditions for finding the maximum sort order
     * Override this method in your model to add custom conditions
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return array
     */
    protected static function getSortOrderConditions($model)
    {
        return [];
    }

    protected static function bootHasSortOrder()
    {
        static::creating(function ($model) {
            $query = static::query();
            
            // Apply any custom conditions
            foreach (static::getSortOrderConditions($model) as $column => $value) {
                $query->where($column, $value);
            }
            
            $maxOrder = $query->max('sort_order') ?? 0;
            $model->sort_order = $maxOrder + 1;
        });
    }
} 