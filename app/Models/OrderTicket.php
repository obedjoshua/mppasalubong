<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderTicket extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $keyType = 'string';
    public $incrementing = false;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($ticket) {
            if (empty($ticket->id)) {
                $ticket->id = static::generateBase64Id();
            }
        });
    }

    public static function generateBase64Id(): string
    {
        do {
            $bytes = random_bytes(8);
            $base64 = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($bytes));
            $id = substr($base64, 0, 11);
        } while (static::where('id', $id)->exists());

        return $id;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderTicketItem::class, 'order_ticket_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}