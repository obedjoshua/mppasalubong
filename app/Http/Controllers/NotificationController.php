<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Auth::user()->notifications()->latest()->get();
        return view('user.notifications', compact('notifications'));
    }

    public function show($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);

        // Mark as read upon viewing detail
        if (isset($notification->is_read)) {
            $notification->update(['is_read' => true]);
        } elseif (isset($notification->read_at) && is_null($notification->read_at)) {
            $notification->markAsRead();
        }

        return view('user.notification-show', compact('notification'));
    }

    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->update(['is_read' => true]);

        return back()->with('success', 'Notification marked as read.');
    }

    public function markAllAsRead()
    {
        Auth::user()->notifications()->where('is_read', false)->update(['is_read' => true]);

        return back()->with('success', 'All notifications marked as read.');
    }

    /**
     * Batch delete selected notifications for the authenticated user.
     */
    public function batchDelete(Request $request)
    {
        $request->validate([
            'notification_ids'   => 'required|array',
            'notification_ids.*' => 'exists:notifications,id',
        ]);

        Auth::user()->notifications()
            ->whereIn('id', $request->notification_ids)
            ->delete();

        return back()->with('success', 'Selected notifications deleted successfully.');
    }
}