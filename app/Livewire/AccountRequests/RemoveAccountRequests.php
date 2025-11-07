<?php

namespace App\Livewire\AccountRequests;

use App\Mail\sendUpdatedUser;
use App\Models\Company;
use App\Models\PriceRules;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class RemoveAccountRequests extends Component
{
    public $user;


    public function mount($id) {

        $this->user = User::where('id', $id)->first();

    }
    public function render()
    {
        if(Auth::user()->is_admin) {
            return view('livewire.accountRequests.removeAccount');
        } else {
            return $this->redirect('/dashboard', navigate: true);
        }
    }
    public function cancelDeleteAccount() {
        return $this->redirect('/accountrequests', navigate: true);
    }

    public function deleteAccount($id) {
        session()->flash('success','Het account is succesvol verwijderd.' );

        User::where('id', $id)->delete();


        return $this->redirect('/accountrequests/', navigate: true);
    }

}
