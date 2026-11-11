@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto my-12 bg-white p-6 rounded-2xl shadow-lg border border-amber-200">
    <div class="text-center">
        <span class="bg-amber-100 text-amber-800 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">
            TESTING / DUMMY GATEWAY
        </span>
        <h2 class="text-xl font-bold mt-4 text-gray-900">Payment Simulation</h2>
        <p class="text-xs text-gray-500 mt-1">Ticket #{{ $ticket->id }}</p>
        <p class="text-3xl font-black text-gray-900 my-4">
            ₱{{ number_format($ticket->total_price ?? $ticket->total_amount ?? 0, 2) }}
        </p>
    </div>

    <form action="{{ route('orders.complete-dummy-payment', $ticket->id) }}" method="POST" class="mt-6">
        @csrf
        <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 rounded-xl transition shadow-lg shadow-emerald-600/20">
            Simulate Successful Payment
        </button>
    </form>
</div>
@endsection