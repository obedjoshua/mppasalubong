<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'quantity',
        'total_price',
        'status',
    ];

    /**
     * Get the user who made the purchase.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the product that was purchased.
     * (Assuming you have or will create a Product model for your shop items)
     */
    public function product()
    {
        // If your shop items model is named differently (e.g., ShopItem::class), update it here.
        return $this->belongsTo(Product::class); 
    }
}