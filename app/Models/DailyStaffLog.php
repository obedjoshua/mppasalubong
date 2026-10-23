<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyStaffLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'branch',
        'log_type',
        'cash_drawer_amount',
        'completed_orders_count',
        'notes',
        'meta_data',
    ];

    protected $casts = [
        'cash_drawer_amount' => 'decimal:2',
        'completed_orders_count' => 'integer',
        'meta_data' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
