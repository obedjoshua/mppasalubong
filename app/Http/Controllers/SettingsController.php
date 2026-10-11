<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class SettingsController extends Controller
{
    /**
     * Display the settings page.
     */
    public function index()
    {
        return view('user.settings'); // Update this path to match your actual blade file location
    }

    /**
     * Process the settings form submission.
     */
    public function update(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 1. Validate incoming data securely
        $request->validate([
            'name'             => ['required', 'string', 'max:255'],
            'email'            => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'avatar'           => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'], // Max 2MB
            'current_password' => ['nullable', 'required_with:new_password', 'string'],
            'new_password'     => ['nullable', 'string', 'min:8', 'different:current_password'],
            'theme'            => ['nullable', 'string', 'in:light,dark,system'],
            'timezone'         => ['nullable', 'string'],
        ]);

        // 2. Update Standard Profile Info
        $user->name = $request->name;
        $user->email = $request->email;

        // 3. Handle Avatar Image Upload
        if ($request->hasFile('avatar')) {
            // Optional: Delete old avatar from storage if it exists to save space
            if ($user->avatar && Storage::disk('public')->exists(str_replace('storage/', '', $user->avatar))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $user->avatar));
            }
            
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = 'storage/' . $path; 
        }

        // 4. Handle Password Updates
        if ($request->filled('current_password') && $request->filled('new_password')) {
            // Verify current password matches before allowing a change
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'The provided password does not match your current password.']);
            }
            $user->password = Hash::make($request->new_password);
        }

        // 5. Handle Toggles & Checkboxes
        // HTML quirk: unchecked boxes send NOTHING. We use $request->has() to evaluate to true/false.
        $user->two_factor_auth  = $request->has('two_factor_auth');
        $user->notify_email     = $request->has('notify_email');
        $user->notify_push      = $request->has('notify_push');
        $user->notify_sms       = $request->has('notify_sms');
        $user->marketing_emails = $request->has('marketing_emails');
        $user->data_sharing     = $request->has('data_sharing');
        
        // 6. Handle Select Dropdowns
        $user->theme    = $request->theme ?? 'system';
        $user->timezone = $request->timezone ?? 'Asia/Manila';

        // Save everything to the database
        $user->save();

        return back()->with('status', 'Settings updated successfully!');
    }

    /**
     * Handle the destructive account deletion.
     */
    public function destroy(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        Auth::logout();
        $user->delete();

        // Invalidate the session to prevent security loopholes
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('status', 'Your account has been permanently deleted.');
    }
}