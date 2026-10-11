<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FA\Google2FA;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class TwoFactorController extends Controller
{
    public function enable()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $google2fa = new Google2FA();

        // 1. Generate a new secret key for the user
        $secret = $google2fa->generateSecretKey();
        
        // Save it temporarily in the session until they verify it
        session()->put('2fa_secret', $secret);

        // 2. Generate the QR Code URL
        $qrCodeUrl = $google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );

        // 3. Render the QR Code as an SVG
        $renderer = new ImageRenderer(
            new RendererStyle(250),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);
        $qrCodeSvg = $writer->writeString($qrCodeUrl);

        // Return a view showing the QR code
        return view('user.2fa-setup', compact('qrCodeSvg', 'secret'));
    }

    public function verify(Request $request)
    {
        $request->validate(['code' => 'required|numeric']);
        
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $google2fa = new Google2FA();
        $secret = session('2fa_secret');

        // Verify the 6-digit code matches the secret
        $valid = $google2fa->verifyKey($secret, $request->code);

        if ($valid) {
            // It worked! Save the secret to the database and turn 2FA on
            $user->google2fa_secret = $secret;
            $user->two_factor_auth = true;
            $user->save();

            session()->forget('2fa_secret');

            return redirect()->route('settings.index')->with('status', 'Two-Factor Authentication is now enabled!');
        }

        return back()->withErrors(['code' => 'Invalid authentication code. Please try again.']);
    }

    public function disable()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        $user->two_factor_auth = false;
        $user->google2fa_secret = null;
        $user->save();

        return back()->with('status', 'Two-Factor Authentication has been disabled.');
    }
}