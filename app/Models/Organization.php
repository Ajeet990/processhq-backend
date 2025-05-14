<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    protected $fillable = [
        'name',
        'username',
        'email',
        'phone',
        'password',
        'address',
        'state',
        'url',
        'status'
    ];

    // Define any relationships or additional methods here
}
