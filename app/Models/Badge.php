<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'icon_path',
        'description',
        'order_requirement',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_badge')->withPivot('unlocked_at');
    }
}
