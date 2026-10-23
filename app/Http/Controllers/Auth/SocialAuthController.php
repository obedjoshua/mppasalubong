<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    /**
     * Redirect to Google OAuth provider.
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle Google OAuth callback.
     */
    public function handleGoogleCallback(Request $request)
    {
        try {
            // 1. Local SSL Bypass
            $driver = Socialite::driver('google');
            if (config('app.env') === 'local') {
                $driver->setHttpClient(new \GuzzleHttp\Client(['verify' => false]));
            }

            $googleUser = $driver->user();

            // 2. Account Linking (If user is already authenticated)
            if (Auth::check()) {
                /** @var User $user */
                $user = Auth::user();
                $user->google_id = $googleUser->getId();

                if (empty($user->avatar)) {
                    $user->avatar = $googleUser->getAvatar();
                }

                if (is_null($user->email_verified_at)) {
                    $user->email_verified_at = Carbon::now();
                }

                $user->save();

                return redirect()->intended('/orders')->with('success', 'Google account linked successfully!');
            }

            // 3. Find or Create User
            /** @var User|null $user */
            $user = User::where('google_id', $googleUser->getId())->first();

            if (! ($user instanceof User)) {
                $email = $googleUser->getEmail();

                /** @var User|null $existingUser */
                $existingUser = $email ? User::where('email', $email)->first() : null;

                if ($existingUser instanceof User) {
                    $existingUser->forceFill([
                        'google_id' => $googleUser->getId(),
                        'avatar' => $existingUser->avatar ?? $googleUser->getAvatar(),
                        'email_verified_at' => $existingUser->email_verified_at ?? Carbon::now(),
                    ])->save();
                    $user = $existingUser;
                } else {
                    $user = User::forceCreate([
                        'name' => $googleUser->getName() ?? 'Google User',
                        'email' => $email ?? ($googleUser->getId().'@google.local'),
                        'password' => bcrypt(str()->random(16)),
                        'google_id' => $googleUser->getId(),
                        'avatar' => $googleUser->getAvatar(),
                        'email_verified_at' => Carbon::now(),
                    ]);
                }
            }

            if (is_null($user->email_verified_at)) {
                $user->email_verified_at = Carbon::now();
                $user->save();
            }

            // 4. Authenticate & Clean Session
            Auth::login($user, true);
            $request->session()->regenerate();
            $request->session()->forget('requires_challenge');

            return $this->authenticatedRedirect($user);

        } catch (Exception $e) {
            return redirect()->route('login')->with('error', 'Something went wrong with Google Login: '.$e->getMessage());
        }
    }

    /**
     * Redirect to Facebook OAuth provider.
     */
    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    /**
     * Handle Facebook OAuth callback.
     */
    public function handleFacebookCallback(Request $request)
    {
        try {
            // 1. Local SSL Bypass
            $driver = Socialite::driver('facebook');
            if (config('app.env') === 'local') {
                $driver->setHttpClient(new \GuzzleHttp\Client(['verify' => false]));
            }

            $facebookUser = $driver->user();

            // 2. Account Linking (If user is already authenticated)
            if (Auth::check()) {
                /** @var User $user */
                $user = Auth::user();
                $user->facebook_id = $facebookUser->getId();

                if (empty($user->avatar)) {
                    $user->avatar = $facebookUser->getAvatar();
                }

                if (is_null($user->email_verified_at)) {
                    $user->email_verified_at = Carbon::now();
                }

                $user->save();

                return redirect()->intended('/orders')->with('success', 'Facebook account linked successfully!');
            }

            // 3. Find or Create User
            /** @var User|null $user */
            $user = User::where('facebook_id', $facebookUser->getId())->first();

            if (! ($user instanceof User)) {
                $email = $facebookUser->getEmail();

                /** @var User|null $existingUser */
                $existingUser = $email ? User::where('email', $email)->first() : null;

                if ($existingUser instanceof User) {
                    $existingUser->forceFill([
                        'facebook_id' => $facebookUser->getId(),
                        'avatar' => $existingUser->avatar ?? $facebookUser->getAvatar(),
                        'email_verified_at' => $existingUser->email_verified_at ?? Carbon::now(),
                    ])->save();
                    $user = $existingUser;
                } else {
                    $fallbackEmail = $email ?? ($facebookUser->getId().'@facebook.local');

                    $user = User::forceCreate([
                        'name' => $facebookUser->getName() ?? 'Facebook User',
                        'email' => $fallbackEmail,
                        'password' => bcrypt(str()->random(16)),
                        'facebook_id' => $facebookUser->getId(),
                        'avatar' => $facebookUser->getAvatar(),
                        'email_verified_at' => Carbon::now(),
                    ]);
                }
            }

            if (is_null($user->email_verified_at)) {
                $user->email_verified_at = Carbon::now();
                $user->save();
            }

            // 4. Authenticate & Clean Session
            Auth::login($user, true);
            $request->session()->regenerate();
            $request->session()->forget('requires_challenge');

            return $this->authenticatedRedirect($user);

        } catch (Exception $e) {
            return redirect()->route('login')->with('error', 'Something went wrong with Facebook Login: '.$e->getMessage());
        }
    }

    /**
     * Unified role-based redirect logic matching LoginController.
     */
    protected function authenticatedRedirect(User $user)
    {
        if ($user->role === 'staff' || str_ends_with($user->email, '@masagana.ph') || (isset($user->is_staff) && $user->is_staff)) {
            return redirect()->intended(route('staff.dashboard'));
        }

        return redirect()->intended('/orders');
    }
}