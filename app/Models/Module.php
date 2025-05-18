<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    protected $fillable = [
        'name',
        'description',
        'slug',
        'icon',
        'parent_id',
        'status',
    ];
}
