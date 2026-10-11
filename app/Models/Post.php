<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'type', // 'thread', 'review', or 'announcement'
        'content', 
        'images',
        'is_pinned',
        'review_metrics',
        'badge_id'
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'review_metrics' => 'array',
        'images' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class)->whereNull('parent_id')->latest();
    }

    public function reactions()
    {
        return $this->hasMany(Reaction::class);
    }

    public function badge()
    {
        return $this->belongsTo(Badge::class);
    }
}