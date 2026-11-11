<?php

namespace App\Observers;

use App\Models\OrderTicket;
use App\Models\Notification as CustomNotificationModel;

class OrderTicketObserver
{
    public function created(OrderTicket $ticket)
    {
        if ($ticket->user_id) {
            CustomNotificationModel::create([
                'user_id'      => $ticket->user_id,
                'title'        => 'Thank You for Your Purchase!',
                'message'      => "Your order ticket #{$ticket->id} has been received. Our team is now reviewing your items.",
                'type'         => 'order_created',
                'banner_image' => asset('images/community_header_01.png'),
                'action_url'   => route('orders.ticket', $ticket->id),
                'is_read'      => false,
            ]);
        }
    }

    public function updated(OrderTicket $ticket)
    {
        if (!$ticket->user_id) {
            return;
        }

        if ($ticket->wasChanged('status')) {
            $statusMessages = [
                'Approved for Payment' => [
                    'title' => 'Order Ticket Approved!',
                    'message' => "Your order ticket #{$ticket->id} has been approved! You can now proceed to payment.",
                    'type' => 'order_approved'
                ],
                'Approved' => [
                    'title' => 'Order Ticket Approved!',
                    'message' => "Your order ticket #{$ticket->id} has been approved! You can now proceed to payment.",
                    'type' => 'order_approved'
                ],
                'In Production' => [
                    'title' => 'Order In Production',
                    'message' => "Payment received! Order #{$ticket->id} is now being prepared in our bakery kitchen.",
                    'type' => 'order_production'
                ],
                'Ready for Pickup' => [
                    'title' => 'Order Ready for Pickup!',
                    'message' => "Your order #{$ticket->id} is packed and ready! Please pick up your order today.",
                    'type' => 'order_ready'
                ],
                'Completed' => [
                    'title' => 'Order Completed!',
                    'message' => "Order #{$ticket->id} has been picked up/completed. Thank you for choosing Masaganang Pagpapala!",
                    'type' => 'order_completed'
                ],
                'Cancelled' => [
                    'title' => 'Order Ticket Update',
                    'message' => "Your order #{$ticket->id} was updated or cancelled by staff.",
                    'type' => 'order_cancelled'
                ],
            ];

            if (isset($statusMessages[$ticket->status])) {
                $info = $statusMessages[$ticket->status];
                CustomNotificationModel::create([
                    'user_id'      => $ticket->user_id,
                    'title'        => $info['title'],
                    'message'      => $info['message'],
                    'type'         => $info['type'],
                    'banner_image' => asset('images/community_header_01.png'),
                    'action_url'   => route('orders.ticket', $ticket->id),
                    'is_read'      => false,
                ]);
            }
        }
    }
}