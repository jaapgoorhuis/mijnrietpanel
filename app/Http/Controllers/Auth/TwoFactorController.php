<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorController extends Controller
{
    public function show()
    {
        if (!session()->has('2fa_user_id')) {
            return redirect()->route('login');
        }

        return view('auth.two-factor');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'code' => ['required', 'digits:6']
        ]);

        $user = User::find(session('2fa_user_id'));

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
                'code' => 'Ongeldige verificatiecode'
            ]);
        }

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
