<?php

namespace App\Livewire\Companys;

use App\Mail\sendUpdatedUser;
use App\Models\Company;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Livewire\Component;
use function Spatie\LaravelPdf\Support\pdf;

class EditCompanyUsers extends Component
{
    public $users;

    public $company_id;

    public $user;
    public $user_id;

    public $gebruikersnaam;

    public $email;

    public $bedrijfsnaam;
    public $phone;
    public $is_admin;

    public $status;
    public $oldStatus;
    public $companys;

    public $company;

    public function mount($id,$slug) {

        $this->company_id = $slug;
        $this->user_id = $id;

        $this->user = User::where('id', $this->user_id)->first();
        $this->company = Company::where('id', $this->company_id)->first();

        $this->companys = Company::get();

        $this->gebruikersnaam = $this->user->name;
        $this->email = $this->user->email;
        $this->bedrijfsnaam = $this->user->bedrijfsnaam;
        $this->phone = $this->user->phone;
        $this->is_admin = $this->user->is_admin;
        $this->status = $this->user->is_active;
        $this->oldStatus = $this->user->is_active;
    }
     public function render()
     {
         if(Auth::user()->is_admin) {
             return view('livewire.companys.editCompanyUser');
         } else {
             return $this->redirect('/dashboard', navigate: true);
         }
     }

    public function rules(): array
    {
        return [
            'gebruikersnaam' => 'required',
            'email' => 'required|email|unique:users,email,' . $this->user->id,
            'bedrijfsnaam' => 'required',
            'status' => 'required',
            'is_admin' => 'required',
            'phone' => 'required',

        ];
    }

    public function messages(): array
    {
        return [
            'gebruikersnaam.required' => 'De gebruikersnaam is een verplicht veld.',
            'email.required' => 'Het email adres is een verplicht veld.',
            'email.email' => 'Het lijkt erop dat dit geen geldig email adres is',
            'bedrijfsnaam.required' => 'De bedrijfsnaam is een verplicht veld.',
            'status.required' => 'De status is een verplicht veld.',
            'phone.required' => 'Het telefoonnummer is een verplicht veld.',
        ];
    }

    public function updateUser() {
        $this->validate($this->rules());


        User::where('id', $this->user_id)->update([
            'name' => $this->gebruikersnaam,
            'email' => $this->email,
            'bedrijfsnaam' => $this->bedrijfsnaam,
            'is_active' => $this->status,
            'phone' => $this->phone,
            'is_admin' => $this->is_admin
        ]);

        if($this->oldStatus != $this->status) {
            Mail::to($this->email)->send(new sendUpdatedUser($this->status));
            session()->flash('success','De gebruiker is aangepast. Er is een email verstuurd naar het bijbehorende email adres dat de status van het account is geupdate');
        } else {
            session()->flash('success','De gebruiker is aangepast');
        }



        return $this->redirect('/companys/'.$this->company_id.'/users', navigate: true);
    }

    public function cancelEditUser() {
        return $this->redirect('/companys/'.$this->company_id.'/users', navigate: true);
    }
}
