<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $user = User::where('email', $request->email)->first();


        $locale = config('app.locale');

        if (!$user) {
            session()->flash(
                'error',
                'De combinatie van dit email adres en wachtwoord is niet bekend bij ons!'
            );

            return redirect()->route('login');
        }

        if (!$user->is_active) {
            session()->flash(
                'error',
                'Uw account is nog niet actief. Duurt dit langer dan gewenst? Neem dan contact met ons op!'
            );

            return redirect()->route('login');
        }

        if (
            $user->lang !== $locale &&
            !$user->is_admin
        ) {
            if ($user->lang === 'en' && $locale === 'nl') {

                session()->flash(
                    'error',
                    'You have a foreign account. You cannot log in at https://mijn.rietpanel.nl. You need to log in at https://my.rietpanel.com'
                );
            }

            if ($user->lang === 'nl' && $locale === 'en') {

                session()->flash(
                    'error',
                    'U heeft een nederlands account. Je kunt niet inloggen op de buitenlandse versie. Log in op de nederlandse versie: https://mijn.rietpanel.nl'
                );
            }

            return redirect()->route('login');
        }

        // Controleert email + wachtwoord
        $request->authenticate();

        $user = Auth::user();

        /**
         * ADMIN + 2FA INGESCHAKELD
         */
        /**
         * ALLE ADMINS VERPLICHT VIA 2FA FLOW
         */
        if ($user->is_admin) {


            Auth::logout();

            session([
                '2fa_user_id' => $user->id,
            ]);

            // Nog geen 2FA ingesteld
            if (
                !$user->two_factor_enabled ||
                empty($user->two_factor_secret)
            ) {


                return redirect()->route('2fa.setup');
            }

            // 2FA al ingesteld
            return redirect()->route('2fa.verify');
        }

        /**
         * NORMALE LOGIN
         */
        $request->session()->regenerate();

        if (
            $user->is_production_employee &&
            !$user->is_admin
        ) {
            return redirect('/productPlanning');
        }

        return redirect()->intended(
            route('dashboard', absolute: false)
        );
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
