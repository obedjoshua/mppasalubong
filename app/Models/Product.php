<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name', 
        'slug', 
        'description', 
        'price', 
        'image_url', 
        'stock_quantity', 
        'is_active'
    ];
}