<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Models\User;
use App\Models\OrderTicket;
use App\Observers\UserObserver;
use App\Observers\OrderTicketObserver;

class EventServiceProvider extends ServiceProvider
{
    public function boot()
    {
        parent::boot();

        User::observe(UserObserver::class);
        OrderTicket::observe(OrderTicketObserver::class);
    }
}