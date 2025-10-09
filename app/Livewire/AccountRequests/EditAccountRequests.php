<?php

namespace App\Livewire\AccountRequests;

use App\Mail\sendUpdatedUser;
use App\Models\Company;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Livewire\Component;
use function Spatie\LaravelPdf\Support\pdf;

class EditAccountRequests extends Component
{
    public $users;

    public $company_id;

    public $user;
    public $user_id;


    public $bedrijfsnaam;

    public $status;
    public $companys;
    public $gebruikersnaam;
    public $email;
    public $phone;
    public $oldStatus;
    public $oldCompany;

    public function mount($id) {
        $this->user_id = $id;

        $this->user = User::where('id', $this->user_id)->first();


        $this->company_id = $this->user->bedrijf_id;
        $this->gebruikersnaam = $this->user->name;
        $this->email = $this->user->email;
        $this->bedrijfsnaam = $this->user->bedrijfsnaam;
        $this->phone = $this->user->phone;
        $this->status = $this->user->is_active;
        $this->oldStatus = $this->user->is_active;
        $this->oldCompany = $this->user->bedrijf_id;

    }
     public function render()
     {
         $this->companys = Company::get();
         if(Auth::user()->is_admin) {
             return view('livewire.accountRequests.editAccountRequest');
         } else {
             return $this->redirect('/dashboard', navigate: true);
         }
     }

    public function rules(): array
    {
        return [
            'status' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'De status is een verplicht veld.',
        ];
    }

    public function updateUser($id) {
        $this->validate($this->rules());


        User::where('id', $id)->update([
            'bedrijf_id' => $this->company_id,
            'is_active' => $this->status,
        ]);

         if($this->oldCompany != $this->company_id && $this->oldStatus != $this->status) {
            session()->flash('success','De gebruiker is toegevoegd aan een bedrijf, en de status van het account is bijgewerkt. Er is een email verstuurd naar het bijbehorende email adres dat de status van het account is geupdate');
             Mail::to($this->email)->send(new sendUpdatedUser($this->status));
        }
        else if($this->oldCompany != $this->company_id) {
            session()->flash('success','De gebruiker is toegevoegd aan een bedrijf. Je kunt de gebruiker terug vinden onder het kopje bedrijven -> bedrijfsgebruikers');
        }
        else if($this->oldStatus != $this->status) {
            Mail::to($this->email)->send(new sendUpdatedUser($this->status));
            session()->flash('success','De gebruiker is aangepast. Er is een email verstuurd naar het bijbehorende email adres dat de status van het account is geupdate. LET OP! de gebruiker is niet toegekent aan een bedrijf en kan geen orders plaatsen');
        }
        else {
            session()->flash('success','Er zijn geen gegevens aan de gebruiker aangepast.');
        }

        return $this->redirect('/accountrequests', navigate: true);
    }

    public function cancelEditAccountRequest() {
        return $this->redirect('/accountRequests', navigate: true);
    }

    public function addCompany() {
        $companys = Company::where('bedrijfsnaam', $this->bedrijfsnaam)->get();


        if(count($companys)) {
            session()->flash('error','Dit bedrijf bestaat al.');
        }else {
            Company::create([
                'bedrijfsnaam' => $this->bedrijfsnaam,
                'discount' => 0
            ]);
            session()->flash('success','Het bedrijf is toegevoegd.');
        }
    }
}
