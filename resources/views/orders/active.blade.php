<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Active Orders | Masaganang Pagpapala Pasalubong</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('images/logo.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}?v={{ filemtime(public_path('css/welcome.css')) }}">
    <style>
        body {
            background-color: #FFFFFF;
            margin: 0;
            padding: 0;
            font-family: 'Manrope', sans-serif;
            color: #1F2937;
        }

        .active-orders-container {
            max-width: 1440px;
            margin: 0 auto;
            padding: clamp(24px, 4vw, 44px) clamp(16px, 3vw, 36px) 80px clamp(16px, 3vw, 36px);
            box-sizing: border-box;
        }

        .page-top-nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
            flex-wrap: wrap;
            gap: 10px;
        }

        .btn-back-dash {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-family: 'Manrope', sans-serif;
            font-weight: 800;
            font-size: 0.85rem;
            color: #8A38F5;
            text-decoration: none;
        }

        .btn-back-dash:hover {
            text-decoration: underline;
        }

        .page-hero-heading {
            font-family: 'Recoleta-Bold', serif;
            font-size: clamp(2.2rem, 5vw, 3.6rem);
            color: #8A38F5;
            margin: 0 0 24px 0;
            line-height: 1.05;
        }

        .orders-outer-panel {
            background: #FFFFFF;
            border: 2px solid #E5E7EB;
            border-radius: 24px;
            padding: clamp(20px, 3.5vw, 36px);
            box-sizing: border-box;
        }

        .orders-sub-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 0 0 20px 0;
        }

        .orders-sub-header h2 {
            font-family: 'Recoleta-Bold', serif;
            font-size: clamp(1.3rem, 2.2vw, 1.7rem);
            color: #310669;
            margin: 0;
            line-height: 1.1;
        }

        .orders-sub-header.is-completed-header h2 {
            color: #065F46;
        }

        .count-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 26px;
            height: 26px;
            padding: 0 8px;
            border-radius: 999px;
            font-family: 'Manrope', sans-serif;
            font-size: 0.8rem;
            font-weight: 800;
        }

        .count-pill.purple {
            background: #F3E8FF;
            color: #8A38F5;
        }

        .count-pill.green {
            background: #DEF7EC;
            color: #03543F;
        }

        .orders-cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(310px, 1fr));
            gap: 18px;
            margin-bottom: 36px;
        }

        .orders-cards-grid:last-child {
            margin-bottom: 0;
        }

        .order-item-card {
            background: #FFFFFF;
            border: 1.5px solid #E5E7EB;
            border-radius: 18px;
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 14px;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.03);
            transition: transform 0.15s ease, box-shadow 0.15s ease;
            box-sizing: border-box;
        }

        .order-item-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.06);
        }

        .order-card-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 10px;
            padding-bottom: 12px;
            border-bottom: 1px dashed #E5E7EB;
        }

        .order-card-code {
            display: block;
            font-size: 0.85rem;
            font-weight: 800;
            color: #8A38F5;
            margin-bottom: 3px;
        }

        .order-card-code.is-green {
            color: #059669;
        }

        .order-card-name {
            margin: 0;
            font-size: 1.02rem;
            font-weight: 800;
            color: #111827;
            line-height: 1.3;
        }

        .order-status-badge {
            font-size: 0.68rem;
            font-weight: 800;
            text-transform: uppercase;
            padding: 5px 10px;
            border-radius: 8px;
            letter-spacing: 0.03em;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .badge-production {
            background: #FEF3C7;
            color: #92400E;
        }

        .badge-ready {
            background: #DEF7EC;
            color: #03543F;
        }

        .badge-completed {
            background: #F3E8FF;
            color: #6B21A8;
        }

        .order-card-meta {
            display: flex;
            flex-direction: column;
            gap: 6px;
            font-size: 0.84rem;
            color: #4B5563;
        }

        .order-card-meta strong {
            color: #1F2937;
            font-weight: 800;
        }

        .order-card-footer {
            display: flex;
            gap: 10px;
            margin-top: 4px;
        }

        .btn-track-order {
            flex: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #8A38F5;
            color: #FFFFFF;
            font-weight: 800;
            font-size: 0.85rem;
            padding: 11px 16px;
            border-radius: 12px;
            text-decoration: none;
            transition: background 0.15s ease;
        }

        .btn-track-order:hover {
            background: #7225D8;
        }

        .btn-track-order.is-emerald {
            background: #10B981;
        }

        .btn-track-order.is-emerald:hover {
            background: #059669;
        }

        .empty-orders-note {
            font-size: 0.92rem;
            color: #6B7280;
            margin: 0 0 32px 0;
        }

        .empty-orders-note:last-child {
            margin-bottom: 0;
        }

        @media (max-width: 600px) {
            .orders-cards-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

    <input type="checkbox" id="menu-toggle" style="display: none;" autocomplete="off">
    @include('partials.side-menu')

    <div class="page-wrapper" id="page-wrapper" style="background-color: #FFFFFF;">
        @include('partials.header')

        @php
            $userTickets = Auth::user()->tickets()->with('items')->latest()->get();

            $activeStatuses = [
                'in_production',
                'in production',
                'preparing',
                'ready_for_pickup',
                'ready for pickup',
                'ready',
            ];

            $activeOrders = $userTickets->filter(function ($t) use ($activeStatuses) {
                return in_array(strtolower(trim($t->status ?? '')), $activeStatuses, true);
            });

            $completedOrders = $userTickets->filter(function ($t) {
                return strtolower(trim($t->status ?? '')) === 'completed';
            });
        @endphp

        <div class="active-orders-container">
            <div class="page-top-nav">
                <a href="{{ route('dashboard') }}" class="btn-back-dash">← Back to Dashboard</a>
                <a href="{{ route('orders.index') }}" class="btn-back-dash">Custom Order Hub →</a>
            </div>

            <h1 class="page-hero-heading">Active Orders</h1>

            <div class="orders-outer-panel">
                <!-- Active In-Production & Ready Orders -->
                <div class="orders-sub-header">
                    <h2>In Production & Ready</h2>
                    <span class="count-pill purple">{{ $activeOrders->count() }}</span>
                </div>

                @if($activeOrders->isEmpty())
                    <p class="empty-orders-note">You have no orders currently in production or ready for pickup.</p>
                @else
                    <div class="orders-cards-grid">
                        @foreach($activeOrders as $order)
                            @php
                                $normStatus = strtolower(trim(str_replace('_', ' ', $order->status ?? '')));
                                $isReady = in_array($normStatus, ['ready for pickup', 'ready'], true);
                                $totalItems = $order->items->sum('quantity');
                            @endphp
                            <div class="order-item-card">
                                <div>
                                    <div class="order-card-header">
                                        <div>
                                            <span class="order-card-code">Order #MP-{{ $order->id }}</span>
                                            <h3 class="order-card-name">
                                                {{ $order->items->pluck('item_type')->map(fn($t) => ucfirst(str_replace('_', ' ', $t)))->join(', ') }}
                                            </h3>
                                        </div>
                                        <span class="order-status-badge {{ $isReady ? 'badge-ready' : 'badge-production' }}">
                                            {{ ucwords(str_replace('_', ' ', $order->status)) }}
                                        </span>
                                    </div>

                                    <div class="order-card-meta" style="margin-top: 12px;">
                                        <div>
                                            <strong>Pickup:</strong> {{ \Carbon\Carbon::parse($order->pickup_date)->format('M j, Y') }} at {{ $order->pickup_time }}
                                        </div>
                                        <div>
                                            <strong>Branch:</strong> {{ $order->pickup_branch === 'san_jose' ? 'San Jose Branch' : 'Main Shop' }}
                                        </div>
                                        <div>
                                            <strong>Items:</strong> {{ $totalItems }} total item(s)
                                        </div>
                                    </div>
                                </div>

                                <div class="order-card-footer">
                                    <a href="{{ route('orders.ticket', $order->id) }}" class="btn-track-order {{ $isReady ? 'is-emerald' : '' }}">
                                        Track Order
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <!-- Completed Orders Section -->
                <div class="orders-sub-header is-completed-header">
                    <h2>Completed Orders</h2>
                    <span class="count-pill green">{{ $completedOrders->count() }}</span>
                </div>

                @if($completedOrders->isEmpty())
                    <p class="empty-orders-note">No completed orders yet.</p>
                @else
                    <div class="orders-cards-grid">
                        @foreach($completedOrders as $order)
                            @php
                                $totalItems = $order->items->sum('quantity');
                            @endphp
                            <div class="order-item-card">
                                <div>
                                    <div class="order-card-header">
                                        <div>
                                            <span class="order-card-code is-green">Order #MP-{{ $order->id }}</span>
                                            <h3 class="order-card-name">
                                                {{ $order->items->pluck('item_type')->map(fn($t) => ucfirst(str_replace('_', ' ', $t)))->join(', ') }}
                                            </h3>
                                        </div>
                                        <span class="order-status-badge badge-completed">Completed</span>
                                    </div>

                                    <div class="order-card-meta" style="margin-top: 12px;">
                                        <div>
                                            <strong>Picked Up:</strong> {{ \Carbon\Carbon::parse($order->pickup_date)->format('M j, Y') }} at {{ $order->pickup_time }}
                                        </div>
                                        <div>
                                            <strong>Branch:</strong> {{ $order->pickup_branch === 'san_jose' ? 'San Jose Branch' : 'Main Shop' }}
                                        </div>
                                        <div>
                                            <strong>Items:</strong> {{ $totalItems }} total item(s)
                                        </div>
                                    </div>
                                </div>

                                <div class="order-card-footer">
                                    <a href="{{ route('orders.ticket', $order->id) }}" class="btn-track-order is-emerald">
                                        View Receipt
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        document.getElementById('page-wrapper').addEventListener('click', function(e) {
            const toggle = document.getElementById('menu-toggle');
            if (toggle && toggle.checked && e.target.closest('#menu-btn') === null) {
                toggle.checked = false;
            }
        });
    </script>
</body>
</html>