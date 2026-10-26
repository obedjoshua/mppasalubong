@extends('layouts.app')

@section('title', 'Ticket #' . $ticket->id)

@push('styles')
    <link rel="preload" href="{{ asset('css/tickets-show.css') }}" as="style">
    <link rel="stylesheet" href="{{ asset('css/tickets-show.css') }}" media="print" onload="this.media='all'">
@endpush

@push('scripts')
    <!-- Register Service Worker for offline capability -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('{{ asset('sw.js') }}')
                    .then(registration => {
                        console.log('SW registered:', registration.scope);
                    })
                    .catch(error => {
                        console.log('SW registration failed:', error);
                    });
            });
        }
    </script>
@endpush

@section('content')
<div class="ticket-page-wrapper">

    <div class="ticket-nav-bar">
        <a href="{{ route('orders.index') }}" class="btn-back-link">
            ← Back to Orders
        </a>
        <span style="font-size: 0.85rem; color: #6b21a8; font-weight: bold;">Order Ticket View</span>
    </div>

    <div class="notice-banner">
        <div style="font-size: 1.3rem; line-height: 1;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 24px; height: 24px;">
                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
            </svg>
        </div>
        <div class="notice-content">
            <strong>Inquiry Received!</strong>
            @if($ticket->preferred_notification_channel === 'messenger')
                <p>We've logged your custom order ticket. Updates will be sent directly to your <strong>Messenger</strong> inbox.</p>
            @else
                <p>We've logged your custom order ticket. Notifications regarding price approval will be sent via <strong>Email ({{ $ticket->customer_email ?? 'your email' }})</strong> and <strong>SMS</strong>.</p>
            @endif
        </div>
    </div>

    @if(session('just_submitted') || true)
        <div class="dashboard-redirect-prompt">
            <div class="prompt-text">
                <strong>Need to make edits or changes to your inquiry?</strong>
                <p>You can review, edit, or delete pending order tickets at any time directly from your user dashboard directory.</p>
            </div>
            <a href="{{ route('dashboard') }}" class="btn-dashboard-redirect">
                Go to Dashboard →
            </a>
        </div>
    @endif

    {{-- PRIMARY TICKET DISPLAY IMAGE --}}
    @php
        $pinnedPreviewImage = null;

        foreach ($ticket->items as$item) {
            $pinnedPreviewImage =$item->image_data 
                ?? $item->r2_url 
                ?? $item->reference_image_path 
                ?? ($item->custom_options['reference_image'] ?? null);

            if ($pinnedPreviewImage) {
                break;
            }
        }
    @endphp

    <div class="ticket-outer-container">
        <div class="ticket-card">
            
            <div class="ticket-main-section">

                @if($pinnedPreviewImage)
                    <div class="pinned-ref-container" onclick="openSliderOverlay()">
                        <div class="polaroid-frame">
                            <svg class="paperclip-svg" viewBox="0 0 500 600" width="100%" height="100%">
                                <defs>
                                    <linearGradient id="metal-grad" x1="0%" y1="0%" x2="100%" y2="100%">
                                        <stop offset="0%" stop-color="#f0f0f0"/>
                                        <stop offset="20%" stop-color="#ffffff"/>
                                        <stop offset="50%" stop-color="#555555"/>
                                        <stop offset="80%" stop-color="#ffffff"/>
                                        <stop offset="100%" stop-color="#777777"/>
                                    </linearGradient>
                                </defs>
                                <g transform="rotate(-15, 250, 300)">
                                    <path d="M 230 220 L 160 440 A 35 35 0 0 0 230 440 L 330 140 A 25 25 0 0 0 290 115 C 275 115 270 125 275 130" 
                                          fill="none" stroke="url(#metal-grad)" stroke-width="16" stroke-linecap="round" stroke-linejoin="round" />
                                </g>
                            </svg>

                            <div class="polaroid-img-box">
                                <img src="{{ $pinnedPreviewImage }}" alt="Ref Image" class="polaroid-img"  />
                            </div>
                            <div class="polaroid-caption">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 14px; height: 14px; vertical-align: middle; margin-right: 4px;">
                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                <circle cx="12" cy="10" r="3"></circle>
            </svg>
            Ref Image
        </div>
                        </div>
                    </div>
                @endif
                
                <div class="ticket-header-row">
                    <div class="brand-title-group">
                        <img src="{{ asset('images/logo.png') }}" alt="Bakery Logo" style="max-height: 48px;" onerror="this.style.display='none'" />
                        <div>
                            <strong style="font-family: 'Recoleta-Bold', serif; font-size: 1.15rem; color: #111827; display: block;">Masaganang Pagpapala Pasalubong</strong>
                            <small style="color: #64748b;">Official Order Inquiry Receipt</small>
                        </div>
                    </div>
                    <div class="badge-status-group">
                        <div class="ticket-number-badge">#{{ $ticket->id }}</div>
                        <div><span class="status-badge">{{ $ticket->status }}</span></div>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: auto 1fr; gap: 6px 14px; font-size: 0.85rem; margin-bottom: 12px;">
                    <strong style="font-family: 'Recoleta-Bold', serif;">Customer:</strong>
                    <span>{{ $ticket->customer_name }}</span>

                    <strong style="font-family: 'Recoleta-Bold', serif;">Pickup Branch:</strong>
                    <span style="color: #8a38f5; font-weight: bold;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 14px; height: 14px; vertical-align: middle; margin-right: 4px;">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                            <circle cx="12" cy="10" r="3"></circle>
                        </svg>
                        {{ $ticket->pickup_branch === 'san_jose' ? 'San Jose Branch' : 'Main Shop' }}
                        @if($ticket->calculated_distance_km)
                            <small style="color: #64748b; font-weight: normal;">({{ $ticket->calculated_distance_km }} km away)</small>
                        @endif
                    </span>

                    <strong style="font-family: 'Recoleta-Bold', serif;">Pickup Date:</strong>
                    <span>{{ \Carbon\Carbon::parse($ticket->pickup_date)->format('F j, Y') }}</span>

                    <strong style="font-family: 'Recoleta-Bold', serif;">Pickup Time:</strong>
                    <span>{{ $ticket->pickup_time }}</span>

                    @if($ticket->location_address &&$ticket->location_address !== 'TBD')
                        <strong style="font-family: 'Recoleta-Bold', serif;">Location:</strong>
                        <span>{{ $ticket->location_address }}</span>
                    @endif
                </div>

                <table>
                    <thead>
                        <tr style="background: #f1f5f9; text-align: left;">
                            <th style="width: 10%">#</th>
                            <th>Item Description</th>
                            <th class="numeric" style="width: 20%">Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ticket->items as $index =>$item)
                            <tr style="border-bottom: 1px solid #e2e8f0;">
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <strong>{{ ucfirst(str_replace('_', ' ', $item->item_type)) }}</strong>
                                </td>
                                <td class="numeric">{{ $item->quantity }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                @if(isset($isDashboardView) && $isDashboardView && in_array(strtolower($ticket->status), ['pending staff review', 'pending']))
                    <div style="display: flex; gap: 10px; margin-top: 14px; padding-top: 10px; border-top: 1px dashed #e2e8f0;">
                        <a href="{{ route('orders.edit', $ticket->id) }}" style="flex: 1; text-align: center; background: #f3e8ff; color: #7e22ce; padding: 8px; border-radius: 6px; text-decoration: none; font-weight: bold; font-size: 0.825rem;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 14px; height: 14px; vertical-align: middle; margin-right: 4px;">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                        </svg>
                        Edit Inquiry
                        </a>
                        <form action="{{ route('orders.destroy', $ticket->id) }}" method="POST" style="flex: 1;" onsubmit="return confirm('Are you sure you want to cancel and delete this order inquiry?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="width: 100%; background: #fef2f2; color: #dc2626; border: 1px solid #fca5a5; padding: 8px; border-radius: 6px; font-weight: bold; font-size: 0.825rem; cursor: pointer;">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 14px; height: 14px; vertical-align: middle; margin-right: 4px;">
                            <polyline points="3 6 5 6 21 6"></polyline>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                        </svg>
                        Delete Ticket
                            </button>
                        </form>
                    </div>
                @endif

                <div class="accordion-toggle-box" onclick="toggleExpandedBreakdown()">
                    <span class="accordion-toggle-label" id="accordionLabel">View Full Order Breakdown</span>
                    <svg class="chevron-icon" id="chevronIcon" fill="none" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                    </svg>
                </div>

                <div id="expandedBreakdownPanel" class="expanded-breakdown-panel">
                    <div class="expanded-section-title">📋 Complete Order Form Details</div>
                    
                    <div class="expanded-detail-row">
                        <span class="expanded-detail-label">Notification Email:</span>
                        <span class="expanded-detail-value">{{ $ticket->customer_email ?? 'N/A' }}</span>
                    </div>
                    <div class="expanded-detail-row">
                        <span class="expanded-detail-label">Contact Phone:</span>
                        <span class="expanded-detail-value">{{ $ticket->customer_phone ?? 'N/A' }}</span>
                    </div>
                    <div class="expanded-detail-row">
                        <span class="expanded-detail-label">Preferred Channel:</span>
                        <span class="expanded-detail-value">{{ ucfirst($ticket->preferred_notification_channel ?? 'Email') }}</span>
                    </div>
                    <div class="expanded-detail-row">
                        <span class="expanded-detail-label">Assigned Branch:</span>
                        <span class="expanded-detail-value">{{ $ticket->pickup_branch === 'san_jose' ? 'San Jose Branch' : 'Main Shop' }}</span>
                    </div>
                    <div class="expanded-detail-row">
                        <span class="expanded-detail-label">Customer Location:</span>
                        <span class="expanded-detail-value">{{ $ticket->location_address ?? 'Store Pickup' }}</span>
                    </div>
                    @if($ticket->customer_latitude &&$ticket->customer_longitude)
                    <div class="expanded-detail-row">
                        <span class="expanded-detail-label">Pinned Coordinates:</span>
                        <span class="expanded-detail-value">{{ $ticket->customer_latitude }}, {{$ticket->customer_longitude }}</span>
                    </div>
                    @endif
                    @if($ticket->calculated_distance_km)
                    <div class="expanded-detail-row">
                        <span class="expanded-detail-label">Distance to Branch:</span>
                        <span class="expanded-detail-value">{{ $ticket->calculated_distance_km }} km</span>
                    </div>
                    @endif

                    <div class="expanded-section-title" style="margin-top: 10px;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 18px; height: 18px; vertical-align: middle; margin-right: 6px;">
                <path d="M12 2L2 7l10 5 10-5-10-5z"></path>
                <path d="M2 17l10 5 10-5"></path>
                <path d="M2 12l10 5 10-5"></path>
            </svg>
            Customization Breakdown
        </div>
                    @foreach($ticket->items as $index =>$item)
                        <div style="margin-bottom: 6px; padding-bottom: 4px; border-bottom: 1px dashed #cbd5e1;">
                            <strong style="font-size: 0.8rem; color: #1e293b;">Item #{{ $index + 1 }}: {{ ucfirst(str_replace('_', ' ',$item->item_type)) }}</strong>
                            @if(!empty($item->custom_options))
                                @foreach($item->custom_options as $key =>$value)
                                    @if(!in_array($key, ['reference_image', 'custom_tag_path']) && is_string($value))
                                        <div class="expanded-detail-row">
                                            <span class="expanded-detail-label">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span>
                                            <span class="expanded-detail-value">{{ $value }}</span>
                                        </div>
                                    @endif
                                @endforeach
                            @endif
                        </div>
                    @endforeach
                </div>

            </div>

            <div class="ticket-stub-divider"></div>

            <div class="ticket-stub-section">
                @php
                    $fallbackQrUrl =$qrData['fallback_url'] ?? '';
                @endphp
                <div class="qr-wrapper-block">
                    <div class="qr-container">
                        @if(isset($qrData) &&$qrData['mode'] === 'inline_svg')
                            {!! $qrData['content'] !!}
                        @else
                            <img id="ticketQrImage" 
                                 src="{{ $qrData['primary_url'] ?? '#' }}" 
                                 alt="Verification QR" 
                                 onerror="this.onerror=null; this.src='{{ $fallbackQrUrl }}';" />
                        @endif
                    </div>
                    <small style="font-size: 0.725rem; color: #64748b; font-weight: bold;">Scan for staff verification</small>
                </div>

                <div style="margin-top: 20px; font-size: 0.775rem; color: #64748b; line-height: 1.3;">
                    <strong style="font-family: 'Recoleta-Bold', serif; color: #111827; font-size: 0.9rem; display: block;">~ Masaganang Pagpapala ~</strong>
                    <address style="font-style: normal; margin-top: 2px;">Salamat sa iyong pagtangkilik!</address>
                </div>
            </div>

        </div>
    </div>

</div>

{{-- TABBED OVERLAY MODAL DRAWER --}}
@if(count($ticket->items) > 0)
<div id="itemSliderOverlay" class="dark-overlay-modal" onclick="closeSliderOverlay(event)">
    <div class="overlay-card-wrapper" onclick="event.stopPropagation()">
        
        <button type="button" class="overlay-close-btn" onclick="closeSliderOverlay(null)">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width: 20px; height: 20px;">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>

        {{-- TAB NAVIGATION HEADER --}}
        <div class="modal-tabs-header">
            @foreach($ticket->items as $idx =>$item)
                <button type="button" 
                        class="tab-btn {{ $idx === 0 ? 'active-tab' : '' }}" 
                        onclick="switchTab('{{ $idx }}')" 
                        id="tabBtn_{{ $idx }}">
                    Item {{ $idx + 1 }}: {{ ucfirst(str_replace('_', ' ',$item->item_type)) }}
                </button>
            @endforeach
        </div>

        @foreach($ticket->items as $idx =>$item)
            @php
                $itemImage =$item->image_data 
                    ?? $item->r2_url 
                    ?? $item->reference_image_path 
                    ?? ($item->custom_options['reference_image'] ?? null);

                if (!empty($itemImage) && is_string($itemImage) && !str_starts_with($itemImage, 'data:image') && !str_starts_with($itemImage, 'http://') && !str_starts_with($itemImage, 'https://')) {
                    if (str_starts_with($itemImage, 'base64,')) {
                        $itemImage = 'data:image/jpeg;' .$itemImage;
                    } else {
                        $cleanPath = ltrim(str_replace('public/', '',$itemImage), '/');
                        $itemImage = asset('storage/' .$cleanPath);
                    }
                }

                $itemImages = collect([
                    $itemImage,
                    $item->custom_options['reference_images'] ?? null,
                ])->flatten()->filter(fn ($image) => is_string($image) && $image !== '')
                  ->unique()->values();
            @endphp
        
            <div class="tab-content-panel {{ $idx === 0 ? 'active-panel' : '' }}" id="tabPanel_{{ $idx }}">
                
                <div class="overlay-left-image">
                    @forelse($itemImages as $image)
                        <img src="{{ $image }}"
                             alt="{{ $item->item_type }} Reference"
                             style="max-width: 100%; max-height: 100%; object-fit: contain; margin-bottom: 8px;"
                                                          onerror="this.style.display='none';" />
                    @empty
                        <div style="color: #94a3b8; font-size: 0.85rem; text-align: center; padding: 20px;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 16px; height: 16px; vertical-align: middle; margin-right: 6px;">
                            <path d="M12 2L2 7l10 5 10-5-10-5z"></path>
                            <path d="M2 17l10 5 10-5"></path>
                            <path d="M2 12l10 5 10-5"></path>
                        </svg>
                        No Reference Image Attached
                        </div>
                    @endforelse
                </div>
        
                <div class="overlay-right-details">
                    <span class="slide-badge">Item {{ $idx + 1 }} of {{ count($ticket->items) }}</span>
                    <h3 class="slide-title">
                        {{ ucfirst(str_replace('_', ' ', $item->item_type)) }}
                    </h3>
        
                    <div style="border-top: 1px solid #e2e8f0; padding-top: 10px; display: flex; flex-direction: column; gap: 6px;">
                        <div class="expanded-detail-row">
                            <span class="expanded-detail-label">Quantity:</span>
                            <span class="expanded-detail-value">{{ $item->quantity }}</span>
                        </div>
        
                        @if(!empty($item->custom_options))
                            @foreach($item->custom_options as $key =>$value)
                                @if(!in_array($key, ['reference_image', 'custom_tag_path']) && is_string($value))
                                    <div class="expanded-detail-row">
                                        <span class="expanded-detail-label">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span>
                                        <span class="expanded-detail-value">{{ $value }}</span>
                                    </div>
                                @endif
                            @endforeach
                        @endif
                    </div>
        
                    <div style="margin-top: 16px; padding: 8px 12px; background: #f8fafc; border-radius: 6px; font-size: 0.75rem; color: #64748b;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 14px; height: 14px; vertical-align: middle; margin-right: 4px;">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                            <circle cx="12" cy="10" r="3"></circle>
                        </svg>
                        Specifications tied to <strong>Ticket #{{ $ticket->id }}</strong>
                    </div>
                </div>
        
            </div>
        @endforeach

    </div>
</div>
@endif

<script>
    let activeScrollY = 0;

    function toggleExpandedBreakdown() {
        const panel = document.getElementById('expandedBreakdownPanel');
        const label = document.getElementById('accordionLabel');
        const icon = document.getElementById('chevronIcon');
        
        if (panel.classList.contains('show')) {
            panel.classList.remove('show');
            icon.classList.remove('rotate');
            label.innerText = 'View Full Order Breakdown';
        } else {
            panel.classList.add('show');
            icon.classList.add('rotate');
            label.innerText = 'Collapse Order Details';
        }
    }

    function openSliderOverlay() {
        const modal = document.getElementById('itemSliderOverlay');
        if (modal) {
            activeScrollY = window.scrollY;
            modal.classList.add('active');
            
            document.body.style.position = 'fixed';
            document.body.style.top = `-${activeScrollY}px`;
            document.body.style.width = '100%';
            document.body.classList.add('modal-open');
            document.documentElement.classList.add('modal-open');
        }
    }

    function closeSliderOverlay(event) {
        if (!event || event.target.id === 'itemSliderOverlay') {
            const modal = document.getElementById('itemSliderOverlay');
            if (modal) {
                modal.classList.remove('active');
                
                document.body.style.position = '';
                document.body.style.top = '';
                document.body.style.width = '';
                document.body.classList.remove('modal-open');
                document.documentElement.classList.remove('modal-open');
                window.scrollTo(0, activeScrollY);
            }
        }
    }

    function switchTab(index) {
        document.querySelectorAll('.tab-btn').forEach((btn, i) => {
            btn.classList.toggle('active-tab', i === index);
        });

        document.querySelectorAll('.tab-content-panel').forEach((panel, i) => {
            panel.classList.toggle('active-panel', i === index);
        });
    }

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') closeSliderOverlay(null);
    });
</script>
@endsection