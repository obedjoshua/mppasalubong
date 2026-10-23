<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class ChallengeController extends Controller
{
    // Show the challenge form
    public function notice(Request $request)
    {
        if (! $request->session()->has('requires_challenge')) {
            return redirect()->route('dashboard');
        }

        return view('auth.challenge');
    }

    // Verify the submitted code
    public function verify(Request $request)
    {
        $request->validate(['code' => 'required|numeric']);
        $user = Auth::user();

        $storedCode = Cache::get('challenge_code_'.$user->id);

        if ($storedCode && $storedCode == $request->code) {
            // Success: Remove the lock and reset the login counter
            $request->session()->forget('requires_challenge');
            Cache::forget('login_count_'.$user->id);
            Cache::forget('challenge_code_'.$user->id);

            return redirect()->intended('/orders');
        }

        return back()->withErrors(['code' => 'The code is incorrect or has expired.']);
    }

    // Resend the code
    public function resend(Request $request)
    {
        $user = Auth::user();
        $code = rand(100000, 999999);
        Cache::put('challenge_code_'.$user->id, $code, now()->addMinutes(15));

        Mail::raw("Your new Masaganang Pagpapala security code is: {$code}", function ($message) use ($user) {
            $message->to($user->email)->subject('New Security Code');
        });

        return back()->with('message', 'A new code has been sent to your email.');
    }
}
