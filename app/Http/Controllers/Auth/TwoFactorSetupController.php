<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorSetupController extends Controller
{
    public function show()
    {
        $userId = session('2fa_user_id');



        if (!$userId) {
            return redirect()->route('login');
        }

        $user = User::find($userId);

        if (!$user || !$user->is_admin) {
            return redirect()->route('login');
        }

        $google2fa = new Google2FA();

        if (empty($user->two_factor_secret)) {

            $user->two_factor_secret =
                $google2fa->generateSecretKey();

            $user->save();
        }

        $qrCodeUrl = $google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $user->two_factor_secret
        );

        return view('auth.two-factor-setup', [
            'user' => $user,
            'qrCodeUrl' => $qrCodeUrl,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => ['required', 'digits:6'],
        ]);

        $userId = session('2fa_user_id');

        if (!$userId) {
            return redirect()->route('login');
        }

        $user = User::find($userId);

        if (!$user) {
            return redirect()->route('login');
        }

        $google2fa = new Google2FA();

        $valid = $google2fa->verifyKey(
            $user->two_factor_secret,
            $request->code
        );

        if (!$valid) {

            return back()->withErrors([
                'code' => 'De ingevoerde verificatiecode is ongeldig.',
            ]);
        }

        $user->two_factor_enabled = true;
        $user->save();

        Auth::login($user);

        session()->forget('2fa_user_id');

        $request->session()->regenerate();

        if (
            $user->is_production_employee &&
            !$user->is_admin
        ) {
            return redirect('/productPlanning');
        }

        return redirect()->route('dashboard');
    }
}
