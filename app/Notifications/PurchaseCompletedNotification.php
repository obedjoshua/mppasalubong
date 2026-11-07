<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PurchaseCompletedNotification extends Notification
{
    use Queueable;

    public function __construct(public string $orderNumber, public float $totalAmount) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        $defaultBanners = ['images/community_header_01.png', 'images/notif_header_01.png'];
        
        return [
            'title'       => "Thank You for Your Purchase! 🛍️",
            'message'     => "Your order #{$this->orderNumber} of ₱" . number_format($this->totalAmount, 2) . " has been confirmed.",
            'details'     => "We have received your payment for order #{$this->orderNumber}. Our bakery team is preparing your items. Thank you for choosing Masaganang Pagpapala!",
            'action_url'  => route('orders.index'),
            'banner_path' => $defaultBanners[array_rand($defaultBanners)],
            'type'        => 'purchase_thank_you',
        ];
    }

    public function toArray($notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}