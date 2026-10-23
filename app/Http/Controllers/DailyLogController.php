<?php

namespace App\Http\Controllers;

use App\Models\DailyStaffLog;
use App\Models\OrderTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DailyLogController extends Controller
{
    public function index()
    {
        $logs = DailyStaffLog::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('staff.logs.index', compact('logs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'branch' => 'required|string',
            'log_type' => 'required|in:Shift Open,Shift Close,Spoilage Waste,Stock Adjustment,General Note',
            'cash_drawer_amount' => 'nullable|numeric|min:0',
            'notes' => 'required|string|max:1000',
            'spoilage_reason' => 'nullable|string',
        ]);

        $completedOrdersCount = OrderTicket::whereDate('completed_at', today())
            ->where('pickup_branch', $validated['branch'])
            ->count();

        DailyStaffLog::create([
            'user_id' => Auth::id(),
            'branch' => $validated['branch'],
            'log_type' => $validated['log_type'],
            'cash_drawer_amount' => $validated['cash_drawer_amount'] ?? null,
            'completed_orders_count' => $completedOrdersCount,
            'notes' => $validated['notes'],
            'meta_data' => $validated['spoilage_reason'] ? ['reason' => $validated['spoilage_reason']] : null,
        ]);

        return back()->with('success', 'Daily log entry saved.');
    }
}
