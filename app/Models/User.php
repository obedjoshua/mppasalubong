<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

/**
 * App\Models\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string|null $password
 * @property string|null $phone
 * @property string|null $avatar
 * @property string|null $google_id
 * @property string|null $facebook_id
 * @property string|null $selected_frame
 * @property bool $two_factor_auth
 * @property string|null $google2fa_secret
 * @property string|null $theme
 * @property string|null $timezone
 * @property bool $notify_email
 * @property bool $notify_push
 * @property bool $notify_sms
 * @property bool $marketing_emails
 * @property bool $data_sharing
 * @property string $role
 * @property Carbon|null $email_verified_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|Order[] $orders
 * @property-read Collection|OrderTicket[] $tickets
 * @property-read Collection|Wishlist[] $wishlists
 * @property-read Collection|Notification[] $notifications
 * @property-read Collection|Badge[] $badges
 * @property-read Collection|Post[] $posts
 * @property-read Collection|Comment[] $comments
 * @property-read Collection|Purchase[] $purchases
 */
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'google_id',    // Added for Google OAuth
        'facebook_id',  // Added for Facebook OAuth
        'avatar',       // Added for profile pictures

        // --- Added for Settings, Roles, and 2FA ---
        'selected_frame',
        'two_factor_auth',
        'google2fa_secret',
        'theme',
        'timezone',
        'notify_email',
        'notify_push',
        'notify_sms',
        'marketing_emails',
        'data_sharing',
        'role', // 'customer', 'employee', 'admin'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'google2fa_secret', // Hide the 2FA secret from arrays/JSON
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_auth' => 'boolean', // Ensures this is treated as true/false
        ];
    }

    // --- Original Relationships ---

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get all order inquiry tickets for the user.
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(OrderTicket::class, 'user_id');
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    // --- New Community & Shop Relationships ---

    public function badges(): BelongsToMany
    {
        return $this->belongsToMany(Badge::class, 'user_badge')->withPivot('unlocked_at');
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class); // Assuming you create a Purchase model later for the Shop
    }

    // --- Automated Gamification / Badge Logic ---

    public function checkAndAwardBadges()
    {
        // Count up all the user's activities
        $orderCount = $this->orders()->count();
        $purchaseCount = $this->purchases()->count();
        $postCount = $this->posts()->count();

        $earnedIds = [];

        // 1. Custom Order Badges
        $orderBadges = Badge::where('order_requirement', '>', 0)
            ->where('order_requirement', '<=', $orderCount)
            ->pluck('id')->toArray();
        $earnedIds = array_merge($earnedIds, $orderBadges);

        // 2. Shop Purchase Badges
        $shopBadges = Badge::where('slug', 'like', 'shop_%')->get()->filter(function ($badge) use ($purchaseCount) {
            $req = (int) str_replace('shop_', '', $badge->slug);

            return $purchaseCount >= $req;
        })->pluck('id')->toArray();
        $earnedIds = array_merge($earnedIds, $shopBadges);

        // 3. Community Post Badges
        $postBadges = Badge::where('slug', 'like', 'post_%')->get()->filter(function ($badge) use ($postCount) {
            $req = (int) str_replace('post_', '', $badge->slug);

            return $postCount >= $req;
        })->pluck('id')->toArray();
        $earnedIds = array_merge($earnedIds, $postBadges);

        // Explicitly attach the 'unlocked_at' timestamp to satisfy the database pivot table
        $syncData = [];
        foreach ($earnedIds as $id) {
            $syncData[$id] = ['unlocked_at' => now()];
        }

        // Sync with the database safely
        if (! empty($syncData)) {
            $this->badges()->syncWithoutDetaching($syncData);
        }
    }
}
