<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id', 'user_id', 'customer_name', 'location_address', 'facebook_link',
        'theme', 'shape', 'size', 'height', 'flavor', 
        'reference_image_path', 'color_base', 'color_top', 'color_border', 'color_sprinkles',
        'message_text', 'message_placement', 'custom_image_path', 'custom_image_placement',
        'pickup_date', 'pickup_time', 'notification_preference', 'status', 'final_price'
    ];

    // Use ticket_id instead of ID for URL routing (e.g., /orders/ticket/TKT-XYZ)
    public function getRouteKeyName()
    {
        return 'ticket_id';
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}