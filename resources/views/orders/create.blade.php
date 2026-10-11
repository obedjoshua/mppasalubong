<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Custom Order Ticket | Masaganang Pagpapala</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('images/logo.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
    <link rel="stylesheet" href="{{ asset('css/orders-create.css') }}">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="vision-route" content="{{ url('/api/vision/extract-colors') }}">
</head>
<body class="orders-page">

    <input type="checkbox" id="menu-toggle" class="hidden-element">
    @include('partials.side-menu')

    <div class="page-wrapper" id="page-wrapper">
        @include('partials.header')

        <div class="dashboard-container">
            <div class="create-wrapper">
                
                <div class="page-header">
                    <a href="{{ route('orders.index') }}" class="back-btn">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path></svg>
                    </a>
                    <div>
                        <h1>Build Your Custom Bakery Ticket</h1>
                        <p>Combine custom cakes, cupcakes, and giveaways in a single inquiry.</p>
                    </div>
                </div>

                @if ($errors->any())
                    <div class="form-errors-banner">
                        <ul class="form-errors-list">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('orders.store') }}" method="POST" enctype="multipart/form-data" id="ticketForm">
                    @csrf

                    <div class="duo-3d-card">
                        <h3 class="card-heading">1. Event & Pickup Schedule</h3>
                        
                        <div class="card-grid-2">
                            <div class="input-group">
                                <label class="input-label">Pickup Date</label>
                                <div class="dropdown-wrapper" id="wrap_pickup_date">
                                    <div class="duo-picker-trigger" id="trigger_pickup_date">
                                        <span id="dateTriggerText">Select Pickup Date</span>
                                        <div class="dropdown-caret"></div>
                                    </div>
                                    <div class="duo-dropdown-popover popover-padding">
                                        <div class="cal-header">
                                            <button type="button" class="btn-duo-outline btn-cal-nav" id="btnPrevMonth">&lt;</button>
                                            <span id="calMonthYearTitle">October 2026</span>
                                            <button type="button" class="btn-duo-outline btn-cal-nav" id="btnNextMonth">&gt;</button>
                                        </div>
                                        <div class="cal-grid">
                                            <div class="cal-day-label">Su</div><div class="cal-day-label">Mo</div><div class="cal-day-label">Tu</div>
                                            <div class="cal-day-label">We</div><div class="cal-day-label">Th</div><div class="cal-day-label">Fr</div>
                                            <div class="cal-day-label">Sa</div>
                                        </div>
                                        <div class="cal-grid cal-dates-grid-margin" id="calDatesGrid"></div>
                                    </div>
                                </div>
                                <input type="hidden" name="pickup_date" id="hiddenDate" required>
                            </div>
                            
                            <div class="input-group">
                                <label class="input-label">Pickup Time</label>
                                <div class="dropdown-wrapper" id="wrap_pickup_time">
                                    <div class="duo-picker-trigger" id="trigger_pickup_time">
                                        <span id="timeTriggerText">08:00 AM</span>
                                        <div class="dropdown-caret"></div>
                                    </div>
                                    <div class="duo-dropdown-popover popover-padding">
                                        <div class="popover-title">Select Pickup Time Slot</div>
                                        <div class="time-picker-grid">
                                            <div>
                                                <label class="input-label-sm">HOUR</label>
                                                <div id="timeHourOpts"></div>
                                            </div>
                                            <div>
                                                <label class="input-label-sm">MINUTE</label>
                                                <div id="timeMinOpts"></div>
                                            </div>
                                            <div>
                                                <label class="input-label-sm">AM/PM</label>
                                                <div id="timeAmpmOpts"></div>
                                            </div>
                                        </div>
                                        <button type="button" class="btn-duo-3d btn-confirm-time" id="btnConfirmTime">Confirm Time</button>
                                    </div>
                                </div>
                                <input type="hidden" name="pickup_time" id="hiddenTime" value="08:00 AM" required>
                            </div>
                        </div>

                        <div class="input-group input-group-top-margin">
                            <label class="input-label">Additional Instructions</label>
                            <textarea name="additional_notes" rows="2" class="clean-input" placeholder="e.g. Please handle with care, party starts at 2 PM..."></textarea>
                        </div>
                        <input type="hidden" name="customer_name" value="{{ Auth::check() ? Auth::user()->name : 'Guest' }}">
                    </div>

                    <h3 class="section-title">2. Order Ticket Line Items</h3>
                    <div id="itemsContainer"></div>

                    <div class="duo-3d-card add-card-dashed">
                        <div class="add-card-title">Add items to your inquiry ticket</div>
                        <div class="add-buttons-grid">
                            <button type="button" class="btn-add-large" id="btnAddCake">
                                <span>🎂</span> Custom Cake
                            </button>
                            <button type="button" class="btn-add-large" id="btnAddCupcakes">
                                <span>🧁</span> Cupcakes
                            </button>
                            <button type="button" class="btn-add-large" id="btnAddLoaf">
                                <span>🍞</span> Cheesy Taisan
                            </button>
                            <button type="button" class="btn-add-large" id="btnAddRound">
                                <span>🍰</span> Round Giveaways
                            </button>
                        </div>
                    </div>

                    <div class="submit-action-container">
                        <button type="submit" class="btn-duo-3d btn-submit-ticket">
                            Submit Ticket for Admin Pricing
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/orders-create.js') }}"></script>
</body>
</html>