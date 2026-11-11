@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto bg-white p-6 rounded-2xl shadow-xl border border-gray-100 my-10 text-center">
    <h2 class="text-xl font-bold text-gray-900">Masaganang Pagpapala Pasalubong</h2>
    <p class="text-xs text-gray-400 mt-0.5 mb-4">Guest Order Verification Ticket</p>

    <!-- QR Code Block -->
    <div class="my-5 flex justify-center">
        <div class="p-3 bg-white border border-gray-200 rounded-xl shadow-inner">
            @if(!empty($qrData))
                <img src="{{ $qrData }}" alt="Order QR Code" class="w-44 h-44">
            @elseif(!empty($ticket->qr_code_path))
                <img src="{{ asset('storage/' . $ticket->qr_code_path) }}" alt="Order QR Code" class="w-44 h-44">
            @else
                <div class="w-44 h-44 bg-gray-100 flex items-center justify-center text-xs text-gray-400">
                    QR Code Unavailable
                </div>
            @endif
        </div>
    </div>

    <!-- Hashed Ticket ID -->
    <div class="bg-slate-100 py-2 px-4 rounded-lg inline-block mb-5">
        <span class="text-xs text-gray-500 block uppercase tracking-wider font-semibold">Ticket ID</span>
        <span class="font-mono font-bold text-lg text-slate-800">{{ $ticket->hashed_ticket_number }}</span>
    </div>

    <!-- Hashed Personal Customer Details -->
    <div class="text-left text-xs space-y-2 border-t border-b border-gray-100 py-4 text-gray-600">
        <div class="flex justify-between">
            <span class="text-gray-400">Customer:</span>
            <span class="font-semibold text-gray-800">{{ $ticket->hashed_customer_name }}</span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-400">Email:</span>
            <span class="font-semibold text-gray-800">{{ $ticket->hashed_email }}</span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-400">Phone:</span>
            <span class="font-semibold text-gray-800">{{ $ticket->hashed_phone }}</span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-400">Status:</span>
            <span class="capitalize font-bold text-emerald-600">{{ $ticket->status }}</span>
        </div>
    </div>

    <!-- Order Summary Items -->
    <div class="mt-4 text-left">
        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Order Summary</h4>
        @foreach($ticket->items as $item)
            <div class="flex justify-between text-xs py-1.5 border-b border-gray-50">
                <span class="text-gray-700">{{ $item->quantity }}x {{ $item->item_type }}</span>
                <span class="font-semibold text-gray-900">₱{{ number_format(($item->price ?? 0) * $item->quantity, 2) }}</span>
            </div>
        @endforeach
    </div>
</div>
@endsection