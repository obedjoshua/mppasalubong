<?php

namespace App\Notifications;

use App\Models\OrderTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TicketApprovedNotification extends Notification
{
    use Queueable;

    public function __construct(public OrderTicket $ticket, public string $statusType = 'approved') {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        $isApproved = strtolower($this->statusType) === 'approved';
        
        return [
            'title'       => $isApproved ? "Order Ticket #{$this->ticket->id} Approved! 🎉" : "Order Ticket #{$this->ticket->id} Update",
            'message'     => $isApproved 
                                ? 'Your custom order ticket has been reviewed and approved by staff.' 
                                : 'Your order ticket status was updated or cancelled by staff.',
            'details'     => "Order Ticket #{$this->ticket->id} is now labeled as: " . ucfirst($this->ticket->status) . ". Scheduled pickup date is " . \Carbon\Carbon::parse($this->ticket->pickup_date)->format('F j, Y') . " at " . $this->ticket->pickup_time . ".",
            'action_url'  => route('orders.ticket', $this->ticket->id),
            'banner_path' => $isApproved ? 'images/appr_notif.png' : 'images/rej_notif.png',
            'type'        => 'order_status',
        ];
    }

    public function toArray($notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}   