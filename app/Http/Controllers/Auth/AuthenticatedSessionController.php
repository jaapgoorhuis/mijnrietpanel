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

        $locale = config('app.locale'); // leest APP_LOCALE uit .env


       if($user) {
           if ($user->is_active == 0) {
               session()->flash('error', 'Uw account is nog niet actief. Duurt dit langer dan gewenst? Neem dan contact met ons op!');
               return redirect(route('login', absolute: false));
           }
           else if ($user->lang !== $locale && $user->is_admin == '0') {
               if($user->lang == 'en' && $locale == 'nl') {
                   session()->flash('error', 'You have a foreign account. You cannot log in at https://mijn.rietpanel.nl. You need to log in at https://my.rietpanel.com');
               }
               else if($user->lang == 'nl' && $locale == 'en') {
                   session()->flash('error', 'U heeft een nederlands account. Je kunt niet inloggen op de buitenlandse versie. Log in op de nederlandse versie. https://mijn.rietpanel.nl');
               }

                return redirect(route('login', absolute: false));
           }
           else {
               $request->authenticate();

               $request->session()->regenerate();

               return redirect()->intended(route('dashboard', absolute: false));
           }
       } else {
           session()->flash('error','De combinatie van dit email adres en wachtwoord is niet bekend bij ons!');
           return redirect(route('login', absolute: false));
       }
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
