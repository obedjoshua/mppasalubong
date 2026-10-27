<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            // 1. Check Login Frequency (Challenge Logic - Active in Production Only)
            if (config('app.env') === 'production') {
                $cacheKey = 'login_count_'.$user->id;
                $loginCount = Cache::get($cacheKey, 0);

                if ($loginCount >= 3) {
                    // Generate a 6-digit code
                    $code = rand(100000, 999999);
                    Cache::put('challenge_code_'.$user->id, $code, now()->addMinutes(15));

                    // Set session lock
                    $request->session()->put('requires_challenge', true);

                    // Email the code to the user via Mailjet
                    Mail::raw("Your Masaganang Pagpapala security code is: {$code}", function ($message) use ($user) {
                        $message->to($user->email)
                            ->subject('Security Alert: Verification Required');
                    });

                    return redirect()->route('challenge.notice');
                }

                // Increment login counter (Expires after 12 hours)
                Cache::put($cacheKey, $loginCount + 1, now()->addHours(12));
            }

            // 2. Standard Email Verification Check
            if (is_null($user->email_verified_at)) {
                return redirect()->route('verification.notice');
            }

            // 3. Systemic Role Routing Check
            if ($user->role === 'admin') {
                return redirect()->intended(route('admin.dashboard'));
            }

            if ($user->role === 'developer') {
                return redirect()->intended(route('developer.dashboard'));
            }

            if ($user->role === 'staff') {
                return redirect()->intended(route('staff.dashboard'));
            }

            return redirect()->intended('/orders');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
