<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class NavMenu extends Model
{   
    use SoftDeletes;
    
    protected $fillable = [
        'name',
        'slug',
        'type',
        'target',
        'button_style',
        'sort_order',
    ];
}
