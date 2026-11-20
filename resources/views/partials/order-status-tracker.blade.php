<!-- Order Status Tracker Component -->
@php
    $statuses = [
        'approved'  => 'Approved',
        'preparing' => 'Preparing',
        'ready'     => 'Ready for Pickup',
        'completed' => 'Completed',
    ];

    $currentStatus = strtolower(trim(str_replace('_', ' ', $ticket->status ?? 'pending')));

    // Normalize status variations to standard step levels
    $statusMap = [
        'pending staff review' => 0,
        'pending'              => 0,
        'under review'         => 0,
        'in review'            => 0,
        'approved'             => 1,
        'approved for payment' => 1,
        'in production'        => 2,
        'preparing'            => 2,
        'ready for pickup'     => 3,
        'ready'                => 3,
        'completed'            => 4,
    ];

    $currentLevel = $statusMap[$currentStatus] ?? 0;

    $statusOrder = [
        'approved'  => 1,
        'preparing' => 2,
        'ready'     => 3,
        'completed' => 4,
    ];

    $fillPercent = min(100, max(0, ($currentLevel - 1) * 33.33));

    $branchName = ($ticket->pickup_branch ?? '') === 'san_jose'
        ? 'San Jose Branch (Norberto St., San Jose, San Miguel)'
        : 'Main Branch (Poblacion, San Miguel, Bulacan)';

    $pickupDateFormatted = !empty($ticket->pickup_date)
        ? \Carbon\Carbon::parse($ticket->pickup_date)->format('F j, Y')
        : 'your scheduled date';

    $pickupTimeFormatted = $ticket->pickup_time ?? 'store hours';

    // Stage-specific heading and customer guidance
    $stageMeta = [
        2 => [
            'badge' => 'In Production',
            'title' => 'Our Bakers Are Preparing & Decorating Your Order!',
            'desc'  => "Your payment is confirmed and your pasalubong items are now freshly baked and decorated in our kitchen for {$pickupDateFormatted} ({$pickupTimeFormatted}).",
            'hint'  => 'We will notify you as soon as your order is boxed and ready for pickup.',
        ],
        3 => [
            'badge' => 'Ready for Pickup',
            'title' => 'Your Order Is Packed & Ready!',
            'desc'  => "Head over to {$branchName} during store hours (8:00 AM – 5:30 PM, Mon–Sat) to claim your order.",
            'hint'  => "Present Ticket #{$ticket->id} or your verification QR code below at the counter.",
        ],
        4 => [
            'badge' => 'Completed',
            'title' => 'Order Picked Up & Completed!',
            'desc'  => 'Thank you for ordering from Masaganang Pagpapala Pasalubong. We hope you enjoy your freshly baked treats!',
            'hint'  => 'Need another custom cake or giveaway box? Create a new ticket anytime.',
        ],
    ];

    $activeStage = $stageMeta[$currentLevel] ?? $stageMeta[2];
@endphp

@if($currentLevel >= 2)
<style>
    .order-tracker-box {
        background: #ffffff;
        padding: 24px 28px;
        border-radius: 18px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.04);
        margin-bottom: 24px;
    }
    .order-tracker-top-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 18px;
    }
    .order-tracker-title {
        font-family: 'Recoleta-Bold', Georgia, serif;
        font-size: 1.15rem;
        font-weight: 800;
        color: #1f2937;
        margin: 0;
    }
    .order-tracker-pill {
        background: #ecfdf5;
        color: #047857;
        border: 1px solid #a7f3d0;
        font-size: 0.75rem;
        font-weight: 800;
        padding: 4px 12px;
        border-radius: 999px;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }
    .order-tracker-hero {
        display: flex;
        align-items: center;
        gap: 20px;
        background: linear-gradient(135deg, #f8fafc 0%, #f0fdf4 100%);
        border: 1px solid #d1fae5;
        border-radius: 14px;
        padding: 16px 20px;
        margin-bottom: 26px;
    }
    .order-tracker-visual {
        width: 112px;
        height: 112px;
        flex-shrink: 0;
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid #d1fae5;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.08);
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }
    .order-tracker-hero-copy {
        flex: 1;
    }
    .order-tracker-hero-copy h4 {
        font-family: 'Recoleta-Bold', Georgia, serif;
        font-size: 1.08rem;
        font-weight: 800;
        color: #065f46;
        margin: 0 0 6px 0;
    }
    .order-tracker-hero-copy p {
        font-size: 0.86rem;
        color: #374151;
        line-height: 1.48;
        margin: 0 0 8px 0;
    }
    .order-tracker-hint {
        display: inline-block;
        font-size: 0.78rem;
        font-weight: 700;
        color: #047857;
        background: rgba(16, 185, 129, 0.12);
        padding: 4px 10px;
        border-radius: 8px;
    }

    /* Smooth Vector Animations */
    @keyframes pipingFloat {
        0%, 100% { transform: translate(0px, 0px) rotate(0deg); }
        50% { transform: translate(-6px, 4px) rotate(-6deg); }
    }
    @keyframes sparklePop {
        0%, 100% { transform: scale(0.7); opacity: 0.4; }
        50% { transform: scale(1.15); opacity: 1; }
    }
    @keyframes steamRise {
        0% { transform: translateY(3px); opacity: 0; }
        50% { opacity: 0.85; }
        100% { transform: translateY(-7px); opacity: 0; }
    }
    @keyframes boxBounce {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-4px); }
    }
    @keyframes checkPulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.06); }
    }
    .svg-piping-bag {
        transform-origin: 85px 25px;
        animation: pipingFloat 2.2s ease-in-out infinite;
    }
    .svg-sparkle-1 {
        transform-origin: 28px 34px;
        animation: sparklePop 1.8s ease-in-out infinite;
    }
    .svg-sparkle-2 {
        transform-origin: 94px 66px;
        animation: sparklePop 1.8s ease-in-out 0.9s infinite;
    }
    .svg-steam-a {
        animation: steamRise 2.2s ease-out infinite;
    }
    .svg-steam-b {
        animation: steamRise 2.2s ease-out 1.1s infinite;
    }
    .svg-pickup-box {
        animation: boxBounce 2s ease-in-out infinite;
    }
    .svg-complete-badge {
        transform-origin: 60px 60px;
        animation: checkPulse 2.2s ease-in-out infinite;
    }

    .order-tracker-track {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        position: relative;
        padding: 0 8px;
    }
    .order-tracker-line-bg {
        position: absolute;
        left: 36px;
        right: 36px;
        top: 20px;
        height: 4px;
        background: #e5e7eb;
        border-radius: 999px;
        z-index: 0;
    }
    .order-tracker-line-fill {
        position: absolute;
        left: 36px;
        top: 20px;
        height: 4px;
        background: #10b981;
        border-radius: 999px;
        transition: width 0.5s ease;
        z-index: 1;
        max-width: calc(100% - 72px);
    }
    .order-tracker-step {
        display: flex;
        flex-direction: column;
        align-items: center;
        position: relative;
        z-index: 2;
        min-width: 84px;
        text-align: center;
    }
    .order-tracker-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 0.9rem;
        background: #e5e7eb;
        color: #6b7280;
        transition: all 0.3s ease;
    }
    .order-tracker-step.is-reached .order-tracker-circle {
        background: #059669;
        color: #ffffff;
        box-shadow: 0 4px 10px rgba(5, 150, 105, 0.25), 0 0 0 4px #ecfdf5;
    }
    .order-tracker-label {
        font-size: 0.78rem;
        font-weight: 600;
        margin-top: 10px;
        color: #9ca3af;
        line-height: 1.25;
    }
    .order-tracker-step.is-reached .order-tracker-label {
        color: #047857;
        font-weight: 800;
    }
    @media (max-width: 600px) {
        .order-tracker-box {
            padding: 18px 14px;
        }
        .order-tracker-hero {
            flex-direction: column;
            text-align: center;
            padding: 16px 14px;
        }
        .order-tracker-visual {
            width: 96px;
            height: 96px;
        }
        .order-tracker-step {
            min-width: 64px;
        }
        .order-tracker-label {
            font-size: 0.7rem;
        }
    }
</style>

<div class="order-tracker-box">
    <div class="order-tracker-top-bar">
        <h3 class="order-tracker-title">Order Status Tracker</h3>
        <span class="order-tracker-pill">{{ $activeStage['badge'] }}</span>
    </div>

    <!-- Stage-Based Animated Vector & Details -->
    <div class="order-tracker-hero">
        <div class="order-tracker-visual">
            @if($currentLevel === 2)
                <!-- Stage 2: Fresh Cake Baking & Frosting Decoration -->
                <svg viewBox="0 0 120 120" width="96" height="96" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="60" cy="64" r="44" fill="#ecfdf5"/>
                    <!-- Warm Steam -->
                    <path class="svg-steam-a" d="M40,46 C37,41 43,37 40,32" stroke="#10b981" stroke-width="2.5" stroke-linecap="round" fill="none"/>
                    <path class="svg-steam-b" d="M52,43 C49,38 55,34 52,29" stroke="#34d399" stroke-width="2.5" stroke-linecap="round" fill="none"/>
                    <!-- Cake Pedestal Stand -->
                    <rect x="34" y="96" width="52" height="5" rx="2.5" fill="#94a3b8"/>
                    <rect x="54" y="88" width="12" height="9" fill="#cbd5e1"/>
                    <rect x="26" y="84" width="68" height="5" rx="2.5" fill="#64748b"/>
                    <!-- Cake Sponge Base -->
                    <rect x="32" y="60" width="56" height="24" rx="5" fill="#f59e0b"/>
                    <rect x="32" y="71" width="56" height="4" fill="#fef3c7"/>
                    <!-- Frosting Drip Top -->
                    <path d="M30,60 C30,55 34,53 39,53 L81,53 C86,53 90,55 90,60 L90,64 C90,67 86,67 86,64 C86,61 82,61 82,65 C82,69 77,69 77,64 C77,61 72,61 72,66 C72,70 67,70 67,64 C67,61 62,61 62,65 C62,68 57,68 57,64 C57,61 52,61 52,66 C52,70 47,70 47,64 C47,61 42,61 42,65 C42,68 37,68 37,64 C37,61 30,62 30,60 Z" fill="#ffffff" stroke="#e2e8f0" stroke-width="1.5"/>
                    <!-- Top Cream Swirl & Cherry -->
                    <ellipse cx="56" cy="51" rx="8" ry="4" fill="#f3e8ff" stroke="#c084fc" stroke-width="1.2"/>
                    <circle cx="56" cy="46" r="4" fill="#ef4444"/>
                    <!-- Animated Piping Bag -->
                    <g class="svg-piping-bag">
                        <polygon points="94,16 106,28 73,49" fill="#a855f7" stroke="#7e22ce" stroke-width="1.5" stroke-linejoin="round"/>
                        <path d="M96,14 L108,26" stroke="#e9d5ff" stroke-width="3" stroke-linecap="round"/>
                        <polygon points="73,49 77,45 69,52" fill="#cbd5e1"/>
                    </g>
                    <!-- Sparkles -->
                    <path class="svg-sparkle-1" d="M28,28 L30,33 L35,35 L30,37 L28,42 L26,37 L21,35 L26,33 Z" fill="#fbbf24"/>
                    <path class="svg-sparkle-2" d="M94,60 L95.5,64 L99.5,65.5 L95.5,67 L94,71 L92.5,67 L88.5,65.5 L92.5,64 Z" fill="#10b981"/>
                </svg>
            @elseif($currentLevel === 3)
                <!-- Stage 3: Packed Gift Box Ready for Pickup -->
                <svg viewBox="0 0 120 120" width="96" height="96" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="60" cy="62" r="44" fill="#ecfdf5"/>
                    <g class="svg-pickup-box">
                        <!-- Bakery Box -->
                        <rect x="30" y="50" width="60" height="42" rx="6" fill="#fef3c7" stroke="#d97706" stroke-width="2"/>
                        <rect x="26" y="42" width="68" height="12" rx="4" fill="#fde68a" stroke="#d97706" stroke-width="2"/>
                        <!-- Purple Ribbon -->
                        <rect x="55" y="42" width="10" height="50" fill="#8A38F5"/>
                        <path d="M60,42 C48,30 42,36 52,42 Z" fill="#a855f7"/>
                        <path d="M60,42 C72,30 78,36 68,42 Z" fill="#a855f7"/>
                        <!-- Ready Badge -->
                        <circle cx="84" cy="82" r="14" fill="#10b981" stroke="#ffffff" stroke-width="2.5"/>
                        <path d="M78,82 L82,86 L90,78" stroke="#ffffff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                    </g>
                </svg>
            @else
                <!-- Stage 4: Order Completed -->
                <svg viewBox="0 0 120 120" width="96" height="96" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="60" cy="60" r="44" fill="#ecfdf5"/>
                    <g class="svg-complete-badge">
                        <circle cx="60" cy="60" r="30" fill="#10b981"/>
                        <path d="M47,60 L56,69 L74,51" stroke="#ffffff" stroke-width="4.5" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                    </g>
                    <path class="svg-sparkle-1" d="M28,28 L30,33 L35,35 L30,37 L28,42 L26,37 L21,35 L26,33 Z" fill="#fbbf24"/>
                    <path class="svg-sparkle-2" d="M92,76 L94,81 L99,83 L94,85 L92,90 L90,85 L85,83 L90,81 Z" fill="#8A38F5"/>
                </svg>
            @endif
        </div>

        <div class="order-tracker-hero-copy">
            <h4>{{ $activeStage['title'] }}</h4>
            <p>{{ $activeStage['desc'] }}</p>
            <span class="order-tracker-hint">{{ $activeStage['hint'] }}</span>
        </div>
    </div>

    <!-- Horizontal Step Progress Bar -->
    <div class="order-tracker-track">
        <!-- Progress Line Background -->
        <div class="order-tracker-line-bg"></div>

        <!-- Progress Line Active Fill -->
        <div class="order-tracker-line-fill" style="width: calc({{ $fillPercent }}% - 24px);"></div>

        @foreach($statuses as $key => $label)
            @php
                $level = $statusOrder[$key];
                $isReached = $currentLevel >= $level;
                $isPassed = $currentLevel > $level || ($currentLevel === 4 && $level === 4);
            @endphp
            <div class="order-tracker-step {{ $isReached ? 'is-reached' : '' }}">
                <div class="order-tracker-circle">
                    @if($isPassed)
                        &#10003;
                    @else
                        {{ $loop->iteration }}
                    @endif
                </div>
                <span class="order-tracker-label">
                    {{ $label }}
                </span>
            </div>
        @endforeach
    </div>
</div>
@endif