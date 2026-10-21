<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by_staff_id');
    }

    public function fulfilledBy()
    {
        return $this->belongsTo(User::class, 'fulfilled_by_staff_id');
    }
}