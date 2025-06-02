<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class LatestScope implements Scope
{
    private $column;
    private $direction;

    public function __construct($column = 'id', $direction = 'desc')
    {
        $this->column = $column;
        $this->direction = $direction;
    }

    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $builder->orderBy($this->column, $this->direction);
    }
}
