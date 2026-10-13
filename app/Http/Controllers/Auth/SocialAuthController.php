<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Exception;

class SocialAuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            if (Auth::check()) {
                /** @var \App\Models\User $user */
                $user = Auth::user();
                $user->google_id = $googleUser->getId();
                
                if (empty($user->avatar)) {
                    $user->avatar = $googleUser->getAvatar();
                }
                
                if (is_null($user->email_verified_at)) {
                    $user->email_verified_at = Carbon::now();
                }

                $user->save();
                return redirect()->route('dashboard')->with('success', 'Google account linked successfully!');
            }

            /** @var \App\Models\User|null $user */
            $user = User::where('google_id', $googleUser->getId())->first();

            if ($user instanceof User) {
                if (is_null($user->email_verified_at)) {
                    $user->email_verified_at = Carbon::now();
                    $user->save();
                }

                Auth::login($user, true);
                $request->session()->regenerate();
                return redirect()->route('dashboard'); 
            } else {
                $email = $googleUser->getEmail();
                
                /** @var \App\Models\User|null $existingUser */
                $existingUser = $email ? User::where('email', $email)->first() : null;

                if ($existingUser instanceof User) {
                    $existingUser->update([
                        'google_id' => $googleUser->getId(),
                        'avatar' => $existingUser->avatar ?? $googleUser->getAvatar(),
                        'email_verified_at' => $existingUser->email_verified_at ?? Carbon::now(),
                    ]);
                    Auth::login($existingUser, true);
                } else {
                    /** @var \App\Models\User $newUser */
                    $newUser = User::create([
                        'name' => $googleUser->getName() ?? 'Google User',
                        'email' => $email ?? ($googleUser->getId() . '@google.local'),
                        'google_id' => $googleUser->getId(),
                        'avatar' => $googleUser->getAvatar(),
                        'email_verified_at' => Carbon::now(),
                    ]);
                    Auth::login($newUser, true);
                }

                $request->session()->regenerate();
                return redirect()->route('dashboard'); 
            }
        } catch (Exception $e) {
            return redirect()->route('login')->with('error', 'Something went wrong with Google Login.');
        }
    }

    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function handleFacebookCallback(Request $request)
    {
        try {
            $facebookUser = Socialite::driver('facebook')->user();

            if (Auth::check()) {
                /** @var \App\Models\User $user */
                $user = Auth::user();
                $user->facebook_id = $facebookUser->getId();
                
                if (empty($user->avatar)) {
                    $user->avatar = $facebookUser->getAvatar();
                }

                if (is_null($user->email_verified_at)) {
                    $user->email_verified_at = Carbon::now();
                }
                
                $user->save();
                return redirect()->route('dashboard')->with('success', 'Facebook account linked successfully!');
            }

            /** @var \App\Models\User|null $user */
            $user = User::where('facebook_id', $facebookUser->getId())->first();

            if ($user instanceof User) {
                if (is_null($user->email_verified_at)) {
                    $user->email_verified_at = Carbon::now();
                    $user->save();
                }

                Auth::login($user, true);
                $request->session()->regenerate();
                return redirect()->route('dashboard');
            } else {
                $email = $facebookUser->getEmail();
                
                /** @var \App\Models\User|null $existingUser */
                $existingUser = $email ? User::where('email', $email)->first() : null;

                if ($existingUser instanceof User) {
                    $existingUser->update([
                        'facebook_id' => $facebookUser->getId(),
                        'avatar' => $existingUser->avatar ?? $facebookUser->getAvatar(), 
                        'email_verified_at' => $existingUser->email_verified_at ?? Carbon::now(),
                    ]);
                    Auth::login($existingUser, true);
                } else {
                    $fallbackEmail = $email ?? ($facebookUser->getId() . '@facebook.local');

                    /** @var \App\Models\User $newUser */
                    $newUser = User::create([
                        'name' => $facebookUser->getName() ?? 'Facebook User',
                        'email' => $fallbackEmail,
                        'facebook_id' => $facebookUser->getId(),
                        'avatar' => $facebookUser->getAvatar(),
                        'email_verified_at' => Carbon::now(),
                    ]);
                    Auth::login($newUser, true);
                }

                $request->session()->regenerate();
                return redirect()->route('dashboard');
            }
        } catch (Exception $e) {
            return redirect()->route('login')->with('error', 'Something went wrong with Facebook Login: ' . $e->getMessage());
        }
    }
}