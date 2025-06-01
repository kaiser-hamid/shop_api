<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Authenticatable
{
    use SoftDeletes, HasApiTokens;
    
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'phone',
        'is_blocked',
        'role'
    ];

    protected $hidden = [
        'password',
    ];
}
