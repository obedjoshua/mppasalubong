<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders | Masaganang Pagpapala</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('images/logo.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
    <link rel="stylesheet" href="{{ asset('css/orders-index.css') }}">
</head>
<body class="orders-page">

    <!-- Hidden Checkbox for Side Menu Toggle State -->
    <input type="checkbox" id="menu-toggle" style="display: none;">

    <!-- Side Menu Reveal Container -->
    @include('partials.side-menu')

    <!-- Main Shifting Page Wrapper -->
    <div class="page-wrapper" id="page-wrapper">
        @include('partials.header')

        <div class="dashboard-container">
            <div class="orders-wrapper">
                
                {{-- FLASH NOTIFICATIONS --}}
                @if(session('success'))
                    <div style="background: #DEF7EC; border: 2px solid #31C48D; border-radius: 12px; padding: 14px 18px; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between; gap: 12px;">
                        <span style="font-family: 'Manrope', sans-serif; font-weight: 700; font-size: 0.9rem; color: #03543F;">
                            {{ session('success') }}
                        </span>
                    </div>
                @endif

                <div class="hero-banner">
                    <!-- Carousel Background Images on Left & Right -->
                    <div class="hero-bg-images">
                        <img src="{{ asset('images/carousel/001.jpeg') }}" alt="Cake 1" class="hero-cake cake-l1">
                        <img src="{{ asset('images/carousel/002.jpeg') }}" alt="Cake 2" class="hero-cake cake-l2">
                        <img src="{{ asset('images/carousel/003.jpeg') }}" alt="Cake 3" class="hero-cake cake-l3">
                        <img src="{{ asset('images/carousel/004.jpeg') }}" alt="Cake 4" class="hero-cake cake-l4">

                        <img src="{{ asset('images/carousel/005.jpeg') }}" alt="Cake 5" class="hero-cake cake-r1">
                        <img src="{{ asset('images/carousel/006.jpeg') }}" alt="Cake 6" class="hero-cake cake-r2">
                        <img src="{{ asset('images/carousel/007.jpeg') }}" alt="Cake 7" class="hero-cake cake-r3">
                        <img src="{{ asset('images/carousel/008.jpeg') }}" alt="Cake 8" class="hero-cake cake-r4">
                    </div>

                    <!-- Centered Content Wrapper -->
                    <div class="hero-content">
                        <h1>Craving a Custom Creation?</h1>
                        <p>Upload a reference photo of your dream cake, or browse our inspiration album to let our AI build your perfect order.</p>
                        <div class="hero-actions">
                            <a href="{{ route('orders.create') }}" class="btn-upload">
                                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                Upload Reference
                            </a>
                            <a href="#" class="btn-browse">Browse Album</a>
                        </div>
                    </div>
                </div>

                {{-- SECTION 1: ACTIVE APPROVED ORDERS CAROUSEL --}}
                <div class="section-header" style="display: flex; justify-content: space-between; align-items: flex-end; margin-top: 28px; margin-bottom: 12px;">
                    <div>
                        <h2>Active Orders</h2>
                        <p>Track your confirmed upcoming pickups.</p>
                    </div>
                    @if(!$orders->isEmpty())
                        <div class="carousel-nav-controls">
                            <button type="button" class="carousel-nav-btn" onclick="scrollCarousel('activeOrdersTrack', -1)">‹</button>
                            <button type="button" class="carousel-nav-btn" onclick="scrollCarousel('activeOrdersTrack', 1)">›</button>
                        </div>
                    @endif
                </div>

                <div class="carousel-box">
                    @if($orders->isEmpty())
                        <div style="text-align: center; padding: 12px;">
                            <p style="margin: 0; color: #64748b; font-weight: bold;">No active approved orders yet.</p>
                        </div>
                    @else
                        <div class="carousel-track" id="activeOrdersTrack">
                            @foreach($orders->take(5) as $order)
                                <div class="carousel-card" style="border: 2px solid #DEF7EC;">
                                    <div class="card-top" style="display: flex; justify-content: space-between; align-items: flex-start;">
                                        <div style="overflow: hidden;">
                                            <span class="order-number" style="font-family: 'Manrope', sans-serif; font-weight: 800; color: #03543F;">Order #MP-{{ $order->id }}</span>
                                            <h3 class="order-title" style="margin: 4px 0 0 0; font-size: 1rem; color: #1e293b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                {{ $order->items->pluck('item_type')->map(fn($t) => ucfirst(str_replace('_', ' ', $t)))->join(', ') }}
                                            </h3>
                                        </div>
                                        <span class="badge ready" style="text-transform: uppercase; font-size: 0.65rem; font-weight: bold; padding: 4px 8px; border-radius: 8px; background: #DEF7EC; color: #03543F; flex-shrink: 0;">
                                            Approved
                                        </span>
                                    </div>

                                    <div class="card-bottom" style="display: flex; align-items: center; gap: 10px; border-top: 1px dashed #DEF7EC; padding-top: 10px;">
                                        <div class="icon-circle" style="background: #def7ec; padding: 8px; border-radius: 50%; color: #03543f; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        </div>
                                        <div style="overflow: hidden;">
                                            <span class="pickup-label" style="display: block; font-size: 0.7rem; color: #64748b; font-weight: bold;">Pickup Date</span>
                                            <span class="pickup-time" style="font-size: 0.825rem; font-weight: bold; color: #0f172a; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                {{ \Carbon\Carbon::parse($order->pickup_date)->format('M j, Y') }} at {{ $order->pickup_time }}
                                            </span>
                                        </div>
                                    </div>

                                    <div style="margin-top: 4px;">
                                        <a href="{{ route('orders.ticket', $order->id) }}" style="display: block; text-align: center; background: #10B981; color: #ffffff; padding: 8px; border-radius: 8px; font-weight: bold; font-size: 0.8rem; text-decoration: none;">
                                            View Confirmed Receipt
                                        </a>
                                    </div>
                                </div>
                            @endforeach

                            @if($orders->count() > 5)
                                <a href="{{ route('dashboard') }}" class="carousel-card-more">
                                    <span style="font-size: 1.5rem; color: #10B981;">→</span>
                                    <strong style="color: #03543F; font-size: 0.9rem;">+{{ $orders->count() - 5 }} More Orders</strong>
                                    <span style="font-size: 0.775rem; color: #64748b;">View All on Dashboard</span>
                                </a>
                            @endif
                        </div>
                    @endif
                </div>

                {{-- SECTION 2: PENDING ORDER TICKETS CAROUSEL (BELOW ACTIVE ORDERS) --}}
                <div class="section-header" style="display: flex; justify-content: space-between; align-items: flex-end; margin-top: 28px; margin-bottom: 12px;">
                    <div>
                        <h2>Pending Order Tickets</h2>
                        <p>Inquiry tickets currently under staff review.</p>
                    </div>
                    @if(!$tickets->isEmpty())
                        <div class="carousel-nav-controls">
                            <button type="button" class="carousel-nav-btn" onclick="scrollCarousel('pendingTicketsTrack', -1)">‹</button>
                            <button type="button" class="carousel-nav-btn" onclick="scrollCarousel('pendingTicketsTrack', 1)">›</button>
                        </div>
                    @endif
                </div>

                <div class="carousel-box">
                    @if($tickets->isEmpty())
                        <div style="text-align: center; padding: 12px;">
                            <p style="margin: 0; color: #64748b; font-weight: bold;">You have no pending tickets under review.</p>
                        </div>
                    @else
                        <div class="carousel-track" id="pendingTicketsTrack">
                            @foreach($tickets->take(5) as $ticket)
                                <div class="carousel-card" style="border: 2px solid #e2e8f0;">
                                    <div class="card-top" style="display: flex; justify-content: space-between; align-items: flex-start;">
                                        <div style="overflow: hidden;">
                                            <span class="order-number" style="font-family: 'Manrope', sans-serif; font-weight: 800; color: #8a38f5;">Ticket #{{ $ticket->id }}</span>
                                            <h3 class="order-title" style="margin: 4px 0 0 0; font-size: 1rem; color: #1e293b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                {{ $ticket->items->pluck('item_type')->map(fn($t) => ucfirst(str_replace('_', ' ', $t)))->join(', ') }}
                                            </h3>
                                        </div>
                                        <span class="badge pending" style="text-transform: uppercase; font-size: 0.65rem; font-weight: bold; padding: 4px 8px; border-radius: 8px; background: #FEF3C7; color: #92400E; flex-shrink: 0;">
                                            {{ $ticket->status }}
                                        </span>
                                    </div>

                                    <div class="card-bottom" style="display: flex; align-items: center; gap: 10px; border-top: 1px dashed #e2e8f0; padding-top: 10px;">
                                        <div class="icon-circle" style="background: #f3e8ff; padding: 8px; border-radius: 50%; color: #8a38f5; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        </div>
                                        <div style="overflow: hidden;">
                                            <span class="pickup-label" style="display: block; font-size: 0.7rem; color: #64748b; font-weight: bold;">Pickup Date</span>
                                            <span class="pickup-time" style="font-size: 0.825rem; font-weight: bold; color: #0f172a; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                {{ \Carbon\Carbon::parse($ticket->pickup_date)->format('M j, Y') }} at {{ $ticket->pickup_time }}
                                            </span>
                                        </div>
                                    </div>

                                    <div style="display: flex; gap: 8px; margin-top: 4px;">
                                        <a href="{{ route('orders.ticket', $ticket->id) }}" style="flex: 1; text-align: center; background: #8a38f5; color: #ffffff; padding: 8px; border-radius: 8px; font-weight: bold; font-size: 0.8rem; text-decoration: none;">
                                            View Ticket
                                        </a>
                                        <form action="{{ route('orders.destroy', $ticket->id) }}" method="POST" style="flex: 1;" onsubmit="return confirm('Are you sure you want to cancel this inquiry?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" style="width: 100%; background: #FEF2F2; color: #DC2626; border: 1px solid #FCA5A5; padding: 8px; border-radius: 8px; font-weight: bold; font-size: 0.8rem; cursor: pointer;">
                                                Cancel
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach

                            @if($tickets->count() > 5)
                                <a href="{{ route('tickets.index') }}" class="carousel-card-more">
                                    <span style="font-size: 1.5rem; color: #8a38f5;">→</span>
                                    <strong style="color: #310669; font-size: 0.9rem;">+{{ $tickets->count() - 5 }} More Tickets</strong>
                                    <span style="font-size: 0.775rem; color: #64748b;">View All on Dashboard</span>
                                </a>
                            @endif
                        </div>
                    @endif
                </div>

            </div> <!-- End of .orders-wrapper -->

            <section class="lifestyle-section">
                <div class="shape-divider-top"><svg viewBox="0 0 1200 120" preserveAspectRatio="none"><path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z" class="shape-fill"></path></svg></div>
                
                <h2 class="lifestyle-heading">it's not just a pasalubong,<br>it's a lifestyle</h2>
                
                <!-- Top Carousel: Moving Left -->
                <div class="infinite-carousel-wrapper">
                    <div class="infinite-track track-left">
                        <img src="{{ asset('images/carousel/001.jpeg') }}" alt="Lifestyle 1" class="carousel-img">
                        <img src="{{ asset('images/carousel/002.jpeg') }}" alt="Lifestyle 2" class="carousel-img">
                        <img src="{{ asset('images/carousel/003.jpeg') }}" alt="Lifestyle 3" class="carousel-img">
                        <img src="{{ asset('images/carousel/004.jpeg') }}" alt="Lifestyle 4" class="carousel-img">
                        <img src="{{ asset('images/carousel/005.jpeg') }}" alt="Lifestyle 5" class="carousel-img">
                        <img src="{{ asset('images/carousel/006.jpeg') }}" alt="Lifestyle 6" class="carousel-img">
                        <img src="{{ asset('images/carousel/007.jpeg') }}" alt="Lifestyle 7" class="carousel-img">
                        <img src="{{ asset('images/carousel/008.jpeg') }}" alt="Lifestyle 8" class="carousel-img">
                        <img src="{{ asset('images/carousel/009.jpeg') }}" alt="Lifestyle 9" class="carousel-img">
                        <img src="{{ asset('images/carousel/010.jpeg') }}" alt="Lifestyle 10" class="carousel-img">
                        
                        <img src="{{ asset('images/carousel/001.jpeg') }}" alt="Lifestyle 1" class="carousel-img">
                        <img src="{{ asset('images/carousel/002.jpeg') }}" alt="Lifestyle 2" class="carousel-img">
                        <img src="{{ asset('images/carousel/003.jpeg') }}" alt="Lifestyle 3" class="carousel-img">
                        <img src="{{ asset('images/carousel/004.jpeg') }}" alt="Lifestyle 4" class="carousel-img">
                        <img src="{{ asset('images/carousel/005.jpeg') }}" alt="Lifestyle 5" class="carousel-img">
                        <img src="{{ asset('images/carousel/006.jpeg') }}" alt="Lifestyle 6" class="carousel-img">
                        <img src="{{ asset('images/carousel/007.jpeg') }}" alt="Lifestyle 7" class="carousel-img">
                        <img src="{{ asset('images/carousel/008.jpeg') }}" alt="Lifestyle 8" class="carousel-img">
                        <img src="{{ asset('images/carousel/009.jpeg') }}" alt="Lifestyle 9" class="carousel-img">
                        <img src="{{ asset('images/carousel/010.jpeg') }}" alt="Lifestyle 10" class="carousel-img">
                    </div>
                </div>

                <!-- Bottom Carousel: Moving Right -->
                <div class="infinite-carousel-wrapper">
                    <div class="infinite-track track-right">
                        <img src="{{ asset('images/carousel/011.jpeg') }}" alt="Lifestyle 11" class="carousel-img">
                        <img src="{{ asset('images/carousel/012.jpeg') }}" alt="Lifestyle 12" class="carousel-img">
                        <img src="{{ asset('images/carousel/013.jpeg') }}" alt="Lifestyle 13" class="carousel-img">
                        <img src="{{ asset('images/carousel/014.jpeg') }}" alt="Lifestyle 14" class="carousel-img">
                        <img src="{{ asset('images/carousel/015.jpeg') }}" alt="Lifestyle 15" class="carousel-img">
                        <img src="{{ asset('images/carousel/016.jpeg') }}" alt="Lifestyle 16" class="carousel-img">
                        <img src="{{ asset('images/carousel/017.jpeg') }}" alt="Lifestyle 17" class="carousel-img">
                        <img src="{{ asset('images/carousel/018.jpeg') }}" alt="Lifestyle 18" class="carousel-img">
                        <img src="{{ asset('images/carousel/019.jpeg') }}" alt="Lifestyle 19" class="carousel-img">
                        <img src="{{ asset('images/carousel/020.jpeg') }}" alt="Lifestyle 20" class="carousel-img">
                        
                        <img src="{{ asset('images/carousel/011.jpeg') }}" alt="Lifestyle 11" class="carousel-img">
                        <img src="{{ asset('images/carousel/012.jpeg') }}" alt="Lifestyle 12" class="carousel-img">
                        <img src="{{ asset('images/carousel/013.jpeg') }}" alt="Lifestyle 13" class="carousel-img">
                        <img src="{{ asset('images/carousel/014.jpeg') }}" alt="Lifestyle 14" class="carousel-img">
                        <img src="{{ asset('images/carousel/015.jpeg') }}" alt="Lifestyle 15" class="carousel-img">
                        <img src="{{ asset('images/carousel/016.jpeg') }}" alt="Lifestyle 16" class="carousel-img">
                        <img src="{{ asset('images/carousel/017.jpeg') }}" alt="Lifestyle 17" class="carousel-img">
                        <img src="{{ asset('images/carousel/018.jpeg') }}" alt="Lifestyle 18" class="carousel-img">
                        <img src="{{ asset('images/carousel/019.jpeg') }}" alt="Lifestyle 19" class="carousel-img">
                        <img src="{{ asset('images/carousel/020.jpeg') }}" alt="Lifestyle 20" class="carousel-img">
                    </div>
                </div>
                
                <div class="shape-divider-bottom"><svg viewBox="0 0 1200 120" preserveAspectRatio="none"><path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z" class="shape-fill"></path></svg></div>
            </section>

            @include('partials.footer')
        </div>
    </div>

    <!-- JavaScript Navigation -->
    <script>
        document.getElementById('page-wrapper').addEventListener('click', function(e) {
            const toggle = document.getElementById('menu-toggle');
            if (toggle.checked && e.target.closest('#menu-btn') === null) {
                toggle.checked = false;
            }
        });

        function scrollCarousel(trackId, direction) {
            const track = document.getElementById(trackId);
            if (track) {
                const scrollAmount = 328; // Card width (310px) + gap (18px)
                track.scrollBy({
                    left: direction * scrollAmount,
                    behavior: 'smooth'
                });
            }
        }
    </script>
</body>
</html>