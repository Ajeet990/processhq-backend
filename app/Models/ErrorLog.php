<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ErrorLog extends Model
{
    protected $fillable = [
        'code', 'message', 'exception', 'file', 'line', 'trace', 'ip', 'user_agent', 'user_id'
    ];
}
