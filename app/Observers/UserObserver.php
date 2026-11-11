<?php

namespace App\Observers;

use App\Models\User;
use App\Models\Notification as CustomNotificationModel;

class UserObserver
{
    public function created(User $user)
    {
        // Only send welcome notifications to customers
        if ($user->role === 'customer' || empty($user->role)) {
            CustomNotificationModel::create([
                'user_id'      => $user->id,
                'title'        => 'Welcome to Masaganang Pagpapala!',
                'message'      => 'We are thrilled to have you here! Browse our sweet treats and custom cakes anytime.',
                'details'      => 'We are thrilled to have you here! Browse our sweet treats and custom cakes anytime.',
                'type'         => 'welcome',
                'banner_image' => asset('images/community_header_01.png'),
                'action_url'   => route('shop'),
                'is_read'      => false,
                'data'         => [
                    'title'        => 'Welcome to Masaganang Pagpapala!',
                    'message'      => 'We are thrilled to have you here! Browse our sweet treats and custom cakes anytime.',
                    'type'         => 'welcome',
                    'banner_image' => asset('images/community_header_01.png'),
                    'action_url'   => route('shop'),
                ],
            ]);
        }
    }
}