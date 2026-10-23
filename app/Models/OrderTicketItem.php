<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderTicketItem extends Model
{
    protected $guarded = [];

    // Converts custom options JSON payload into a PHP array
    protected $casts = [
        'custom_options' => 'array',
    ];

    /**
     * Get the order ticket that owns the item.
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(OrderTicket::class, 'order_ticket_id');
    }
}
