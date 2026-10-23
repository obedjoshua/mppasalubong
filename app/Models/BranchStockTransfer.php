<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BranchStockTransfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'transfer_code',
        'from_branch',
        'to_branch',
        'product_id',
        'item_name',
        'quantity',
        'status',
        'requested_by_staff_id',
        'fulfilled_by_staff_id',
        'notes',
        'received_at',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'received_at' => 'datetime',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by_staff_id');
    }

    public function fulfilledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'fulfilled_by_staff_id');
    }
}
