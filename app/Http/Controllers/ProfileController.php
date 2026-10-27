<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * Handle phone submission from dashboard and direct to Firebase OTP verification.
     */
    public function sendPhoneOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
        ]);

        return redirect()->route('profile.otp.form')->with('phone', $request->phone);
    }

    /**
     * Show the Firebase OTP verification view.
     */
    public function showOtpForm()
    {
        return view('user.verify-otp');
    }

    /**
     * Verify Firebase ID Token & Save Phone Number.
     */
    public function verifyOtp(Request $request)
    {
        $validated = $request->validate([
            'phone' => 'required|string|max:20',
            'id_token' => 'required|string',
        ]);

        /** @var User $user */
        $user = Auth::user();

        // Strip non-numeric characters
        $cleanPhone = preg_replace('/[^0-9]/', '', $validated['phone']);
        if (str_starts_with($cleanPhone, '0')) {
            $cleanPhone = substr($cleanPhone, 1);
        }

        $user->update([
            'phone' => $cleanPhone,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Phone number linked successfully!',
        ]);
    }

    /**
     * Edit Profile View.
     */
    public function edit()
    {
        /** @var User $user */
        $user = Auth::user();

        $orderCount = $user->orders()->count();

        $unlockedFrames = ['frame_reg01', 'frame_reg02'];
        if ($orderCount >= 10) {
            $unlockedFrames[] = 'frame_10';
        }
        if ($orderCount >= 20) {
            $unlockedFrames[] = 'frame_20';
        }
        if ($orderCount >= 100) {
            $unlockedFrames[] = 'frame_100';
        }

        if ($user->role === 'admin') {
            $unlockedFrames[] = 'frame_exclusive';
        }

        return view('user.profile-edit', compact('user', 'unlockedFrames', 'orderCount'));
    }

    /**
     * Update Profile Logic.
     */
    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'avatar_file' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'preset_avatar' => 'nullable|string',
            'selected_frame' => 'nullable|string',
        ]);

        /** @var User $user */
        $user = Auth::user();
        $user->name = $request->name;

        // Avatar handling
        if ($request->preset_avatar === 'new_upload' && $request->hasFile('avatar_file')) {
            $path = $request->file('avatar_file')->store('avatars', 'public');
            $user->avatar = '/storage/'.$path;
        } elseif ($request->preset_avatar === 'keep_custom') {
            // Keep existing
        } elseif ($request->preset_avatar === 'default') {
            $user->avatar = null;
        } elseif ($request->filled('preset_avatar')) {
            $user->avatar = '/images/avatars/'.$request->preset_avatar;
        }

        // Frame validation
        $allowedFrames = ['frame_reg01', 'frame_reg02'];
        $orderCount = $user->orders()->count();
        if ($orderCount >= 10) {
            $allowedFrames[] = 'frame_10';
        }
        if ($orderCount >= 20) {
            $allowedFrames[] = 'frame_20';
        }
        if ($orderCount >= 100) {
            $allowedFrames[] = 'frame_100';
        }

        if ($user->role === 'admin') {
            $allowedFrames[] = 'frame_exclusive';
        }

        if (in_array($request->selected_frame, $allowedFrames)) {
            $user->selected_frame = $request->selected_frame;
        }

        $user->save();

        return redirect()->route('dashboard')->with('success', 'Profile updated successfully!');
    }
}
