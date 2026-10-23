<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'order_ticket_id', // Added so order tickets can be linked upon approval
        'order_number',
        'total_amount',
        'status',
    ];

    /**
     * Get the user who owns the order.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all purchased or approved items in this order.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the original inquiry ticket this order originated from.
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(OrderTicket::class, 'order_ticket_id');
    }
}
