<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BranchMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'sender_branch',
        'recipient_branch',
        'subject',
        'message',
        'priority',
        'is_read',
        'order_ticket_id',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(OrderTicket::class, 'order_ticket_id', 'id');
    }
}
