<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id', 
        'product_id', 
        'item_name', 
        'customization_details', 
        'target_delivery_date', 
        'quantity', 
        'price_at_time_of_purchase'
    ];

    /**
     * Get the parent order.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the associated shop product (if applicable).
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}