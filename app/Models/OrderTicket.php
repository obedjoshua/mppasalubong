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

    protected $casts = [
        'pickup_date' => 'date',
        'completed_at' => 'datetime',
        'notification_sent_at' => 'datetime',
        'total_price' => 'decimal:2',
        'deposit_required' => 'decimal:2',
        'customer_latitude' => 'float',
        'customer_longitude' => 'float',
        'calculated_distance_km' => 'float',
        'is_rescheduled' => 'boolean',
        'is_testing' => 'boolean',
    ];

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

    // --- Privacy Hashing Helpers for Guest/Messenger Shared Views ---

    public function getHashedTicketIdAttribute(): string
    {
        $id = $this->id;
        if (strlen($id) <= 4) {
            return 'TCK-***';
        }
        return substr($id, 0, 3) . '***' . substr($id, -2);
    }

    public function getHashedCustomerNameAttribute(): string
    {
        if (empty($this->customer_name)) return 'Guest Customer';
        $words = explode(' ', trim($this->customer_name));
        $hashed = array_map(function ($word) {
            return mb_substr($word, 0, 1) . str_repeat('*', max(1, mb_strlen($word) - 1));
        }, $words);
        return implode(' ', $hashed);
    }

    public function getHashedCustomerEmailAttribute(): string
    {
        if (empty($this->customer_email)) return 'N/A';
        $parts = explode('@', $this->customer_email);
        $name = $parts[0];
        $domain = $parts[1] ?? '';
        $hashedName = substr($name, 0, 1) . str_repeat('*', max(1, strlen($name) - 1));
        return $hashedName . '@' . $domain;
    }

    public function getHashedCustomerPhoneAttribute(): string
    {
        if (empty($this->customer_phone)) return 'N/A';
        $phone = preg_replace('/[^0-9+]/', '', $this->customer_phone);
        if (strlen($phone) <= 6) return '****';
        return substr($phone, 0, 4) . '****' . substr($phone, -2);
    }

    // --- Relationships ---
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

    public function staffHandler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by_staff_id');
    }

    public function branchMessages(): HasMany
    {
        return $this->hasMany(BranchMessage::class, 'order_ticket_id', 'id');
    }

    public function getBranchNameAttribute(): string
    {
        return match ($this->pickup_branch) {
            'san_jose' => 'San Jose Branch',
            default => 'Main Shop',
        };
    }
}