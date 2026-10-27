<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiRequestMetric extends Model
{
    protected $fillable = [
        'method',
        'path',
        'status',
        'duration_ms',
    ];
}
