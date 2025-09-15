<?php

namespace App\Livewire\Surcharges;

use App\Mail\sendUpdatedUser;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class RemoveSurcharges extends Component
{
    public $surcharge;


    public function mount($id) {
        $this->surcharge = \App\Models\Surcharges::where('id', $id)->first();

    }
    public function render()
    {
        if(Auth::user()->is_admin) {
            return view('livewire.surcharges.removeSurcharges');
        } else {
            return $this->redirect('/dashboard', navigate: true);
        }
    }
    public function cancelRemoveSurcharge() {
        return $this->redirect('/surcharges', navigate: true);
    }

    public function deleteSurcharge($id) {
        session()->flash('success','De toeslag is verwijderd');

        \App\Models\Surcharges::where('id', $id)->delete();
        return $this->redirect('/surcharges', navigate: true);
    }

}
