<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderTicketItem extends Model
{
    protected $guarded = [];

    protected $casts = [
        'custom_options' => 'array',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(OrderTicket::class, 'order_ticket_id');
    }

    /**
     * Resilient Image URL Accessor for Reference Images.
     */
    public function getImageUrlAttribute(): ?string
    {
        if (!empty($this->r2_url)) {
            return $this->r2_url;
        }

        if (!empty($this->image_path)) {
            return asset('storage/' . $this->image_path);
        }

        if (!empty($this->reference_image_path)) {
            return str_starts_with($this->reference_image_path, 'http') 
                ? $this->reference_image_path 
                : asset($this->reference_image_path);
        }

        if (!empty($this->image_data)) {
            return $this->image_data;
        }

        return null;
    }
}