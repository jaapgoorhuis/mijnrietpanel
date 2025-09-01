<?php

namespace App\Livewire\Users;

use App\Mail\sendOrder;
use App\Mail\sendUpdatedUser;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Livewire\Component;
use function Spatie\LaravelPdf\Support\pdf;

class EditUsers extends Component
{
    public $user;
    public $userId;

    public $gebruikersnaam;

    public $email;

    public $bedrijfsnaam;
    public $phone;
    public $is_admin;

    public $status;
    public $oldStatus;


    public function mount() {
        $this->userId = Route::current()->parameter('id');
        $this->user = User::where('id', $this->userId)->first();

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
            return view('livewire.users.editUsers');
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
            'phone.required' => 'De status is een verplicht veld.',
        ];
    }

    public function updateUser($id) {
        $this->validate($this->rules());


        User::where('id', $id)->update([
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

        return $this->redirect('/users', navigate: true);
    }

    public function cancelEditUser() {
        return $this->redirect('/users', navigate: true);
    }
}
