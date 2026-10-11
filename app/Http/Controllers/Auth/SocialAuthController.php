<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Exception;

class SocialAuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            if (Auth::check()) {
                /** @var \App\Models\User $user */
                $user = Auth::user();
                $user->google_id = $googleUser->id;
                
                if (empty($user->avatar)) {
                    $user->avatar = $googleUser->avatar;
                }
                
                if (is_null($user->email_verified_at)) {
                    $user->email_verified_at = now();
                }

                $user->save();
                return redirect()->route('dashboard')->with('success', 'Google account linked successfully!');
            }

            $user = User::where('google_id', $googleUser->id)->first();

            if ($user) {
                if (is_null($user->email_verified_at)) {
                    $user->email_verified_at = now();
                    $user->save();
                }

                Auth::login($user, true);
                return redirect()->route('dashboard'); 
            } else {
                $existingUser = User::where('email', $googleUser->email)->first();

                if ($existingUser) {
                    $existingUser->update([
                        'google_id' => $googleUser->id,
                        'avatar' => $existingUser->avatar ?? $googleUser->avatar,
                        'email_verified_at' => $existingUser->email_verified_at ?? now(),
                    ]);
                    Auth::login($existingUser, true);
                } else {
                    $newUser = User::create([
                        'name' => $googleUser->name,
                        'email' => $googleUser->email,
                        'google_id' => $googleUser->id,
                        'avatar' => $googleUser->avatar,
                        'email_verified_at' => now(),
                    ]);
                    Auth::login($newUser, true);
                }

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

    public function handleFacebookCallback()
    {
        try {
            $facebookUser = Socialite::driver('facebook')->user();

            if (Auth::check()) {
                /** @var \App\Models\User $user */
                $user = Auth::user();
                $user->facebook_id = $facebookUser->id;
                
                if (empty($user->avatar)) {
                    $user->avatar = $facebookUser->avatar;
                }

                if (is_null($user->email_verified_at)) {
                    $user->email_verified_at = now();
                }
                
                $user->save();
                return redirect()->route('dashboard')->with('success', 'Facebook account linked successfully!');
            }

            $user = User::where('facebook_id', $facebookUser->id)->first();

            if ($user) {
                if (is_null($user->email_verified_at)) {
                    $user->email_verified_at = now();
                    $user->save();
                }

                Auth::login($user, true);
                return redirect()->route('dashboard');
            } else {
                $existingUser = User::where('email', $facebookUser->email)->first();

                if ($existingUser) {
                    $existingUser->update([
                        'facebook_id' => $facebookUser->id,
                        'avatar' => $existingUser->avatar ?? $facebookUser->avatar, 
                        'email_verified_at' => $existingUser->email_verified_at ?? now(),
                    ]);
                    Auth::login($existingUser, true);
                } else {
                    $newUser = User::create([
                        'name' => $facebookUser->name,
                        'email' => $facebookUser->email,
                        'facebook_id' => $facebookUser->id,
                        'avatar' => $facebookUser->avatar,
                        'email_verified_at' => now(),
                    ]);
                    Auth::login($newUser, true);
                }

                return redirect()->route('dashboard');
            }
        } catch (Exception $e) {
            return redirect()->route('login')->with('error', 'Something went wrong with Facebook Login.');
        }
    }
}