<?php

namespace App\Livewire\Companys;

use App\Mail\sendUpdatedUser;
use App\Models\Company;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;
use function Spatie\LaravelPdf\Support\pdf;

class CreateCompanyUsers extends Component
{
    public $users;
    public $lang = 'nl';
    public $company_id;

    public $user;
    public $user_id;

    public $gebruikersnaam;

    public $email;

    public $bedrijfid;
    public $phone;
    public $is_admin = 0;

    public $status = 0;
    public $oldStatus;
    public $companys;

    public $company;

    public $rol;

    public $password;

    public function mount($id) {



        $this->company = Company::where('id', $id)->first();

        $this->companys = Company::get();

        $this->bedrijfid = $id;


    }
     public function render()
     {
         if(Auth::user()->is_admin) {
             return view('livewire.companys.createCompanyUser');
         } else {
             return $this->redirect('/dashboard', navigate: true);
         }
     }

    public function rules(): array
    {
        return [
            'gebruikersnaam' => 'required',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->where(function ($query) {
                    return $query->where('is_removed', 0);
                }),
            ],
            'status' => 'required',
            'is_admin' => 'required',
            'phone' => 'required',
            'password' => ['required', Password::min(8)->letters()->numbers()],
        ];
    }

    public function messages(): array
    {
        return [
            'gebruikersnaam.required' => 'De gebruikersnaam is een verplicht veld.',

            'email.required' => 'Het e-mailadres is een verplicht veld.',
            'email.unique' => 'Dit e-mailadres bestaat al.',
            'email.email' => 'Dit lijkt geen geldig e-mailadres te zijn.',

            'password.required' => 'Het wachtwoord is een verplicht veld.',
            'password.min' => 'Het wachtwoord moet minimaal :min tekens bevatten.',
            'password.confirmed' => 'De wachtwoorden komen niet overeen.',
            'password.letters' => 'Het wachtwoord moet minimaal één letter bevatten.',
            'password.numbers' => 'Het wachtwoord moet minimaal één cijfer bevatten.',

            'status.required' => 'De status is een verplicht veld.',
            'phone.required' => 'Het telefoonnummer is een verplicht veld.',
        ];
    }


    public function createUser()
    {
        $this->validate($this->rules());

        // Check of gebruiker al bestaat maar verwijderd is
        $existingUser = User::where('email', $this->email)
            ->where('is_removed', 1)
            ->first();

        if ($this->rol == 0) {
            $architect = 0;
            $production_employee = 0;
        } elseif ($this->rol == 1) {
            $architect = 1;
            $production_employee = 0;
        } elseif ($this->rol == 2) {
            $architect = 0;
            $production_employee = 1;
        } else {
            $architect = 0;
            $production_employee = 0;
        }

        if ($existingUser) {
            // 🔄 HERSTEL BESTAANDE USER
            $existingUser->update([
                'name' => $this->gebruikersnaam,
                'bedrijf_id' => $this->bedrijfid,
                'is_active' => $this->status,
                'phone' => $this->phone,
                'bedrijfsnaam' => Company::find($this->bedrijfid)?->bedrijfsnaam,
                'is_admin' => $this->is_admin,
                'is_architect' => $architect,
                'is_production_employee' => $production_employee,
                'password' => Hash::make($this->password),
                'lang' => $this->lang,
                'is_removed' => 0, // 👈 BELANGRIJK
            ]);

            session()->flash('success', 'Deze gebruiker met dit email adres heeft in het verleden bestaan. De gebruiker is hersteld.');
        } else {
            // ➕ NIEUWE USER
            User::create([
                'name' => $this->gebruikersnaam,
                'email' => $this->email,
                'bedrijf_id' => $this->bedrijfid,
                'is_active' => $this->status,
                'phone' => $this->phone,
                'bedrijfsnaam' => Company::find($this->bedrijfid)?->bedrijfsnaam,
                'is_admin' => $this->is_admin,
                'is_architect' => $architect,
                'is_production_employee' => $production_employee,
                'password' => Hash::make($this->password),
                'lang' => $this->lang,
            ]);

            session()->flash('success', 'De gebruiker is toegevoegd.');
        }

        return $this->redirect('/companys/' . $this->bedrijfid . '/users', navigate: true);
    }

    public function cancelCreateUser() {
        return $this->redirect('/companys/'.$this->bedrijfid.'/users', navigate: true);
    }
}
