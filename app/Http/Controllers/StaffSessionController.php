<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class StaffSessionController extends Controller
{
    /**
     * Lock the staff terminal session.
     */
    public function lockSession(Request $request)
    {
        $request->session()->put('staff_terminal_locked', true);

        return redirect()->route('staff.session.locked');
    }

    /**
     * Show the terminal lock screen wall.
     */
    public function showLockWall(Request $request)
    {
        // If the session isn't locked, redirect back to the staff dashboard
        if (! $request->session()->get('staff_terminal_locked', false)) {
            return redirect()->route('staff.dashboard');
        }

        return view('staff.auth.locked');
    }

    /**
     * Unlock the terminal session via staff password verification.
     */
    public function unlockSession(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        $user = Auth::user();

        if (Hash::check($request->password, $user->password)) {
            $request->session()->forget('staff_terminal_locked');

            return redirect()->route('staff.dashboard')
                ->with('success', 'Terminal unlocked successfully.');
        }

        return back()->withErrors([
            'password' => 'Invalid staff password. Terminal remains locked.',
        ]);
    }
}
