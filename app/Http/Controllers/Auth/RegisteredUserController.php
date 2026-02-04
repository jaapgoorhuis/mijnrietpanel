<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\sendNewCustomer;
use App\Mail\sendOrder;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Hibit\GeoDetect;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */



    public function store(Request $request): RedirectResponse
    {

        $geoDetect = new GeoDetect();

        if($_SERVER['REMOTE_ADDR'] !== '127.0.0.1') {
            $country = $geoDetect->getCountry($_SERVER['REMOTE_ADDR']);
            if($country->getIsoCode() != 'NL') {
                $locale = 'EN';
            } else {
                $locale = 'NL';
            }
        } else {
            $locale = config('app.locale'); // leest APP_LOCALE uit .env
        }

        $request->validate($this->rules(), $this->messages());


        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'bedrijfsnaam' => $request->bedrijfsnaam,
            'phone' => $request->phone,
            'is_active' => false,
            'lang' => $locale,
        ]);

        event(new Registered($user));

        Mail::to(env('MAIL_TO_ADDRESS'))->send(new sendNewCustomer($request->email));


        session()->flash('success','Uw aanvraag is succesvol doorgekomen. U krijgt een mail zodra uw account actief is');
        return redirect(route('login', absolute: false));
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'bedrijfsnaam' => ['required'],
            'phone' => ['required'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => __('messages.email_exists'),
            'name.required' => __('messages.name_required'),
            'password.required' => __('messages.password_required'),
            'bedrijfsnaam.required' => __('messages.company_name_required'),
            'phone.required' => __('messages.phone_required'),
            'confirmed' => __('messages.not_confirmed'),
            'password.min' => __('messages.password_not_longenaugh'),
        ];
    }
}
