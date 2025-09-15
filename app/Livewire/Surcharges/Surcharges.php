<?php

namespace App\Livewire\Surcharges;

use App\Mail\sendUpdatedUser;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class Surcharges extends Component
{
    public $surcharges;


    public function render()
    {
        if(Auth::user()->is_admin) {

            $this->surcharges = \App\Models\Surcharges::get();
            return view('livewire.surcharges.surcharges');
        } else {
            return $this->redirect('/dashboard', navigate: true);
        }
    }

    public function newSurcharge() {
        return $this->redirect('/surcharges/create', navigate: true);
    }

    public function editSurcharge($id) {
        return $this->redirect('/surcharges/edit/'.$id, navigate: true);
    }

    public function removeSurcharges($id) {
        if(Auth::user()->is_admin) {
            return $this->redirect('/surcharges/remove/' . $id, navigate: true);
        }
        else {
            return $this->redirect('/dashboard', navigate: true);
        }
    }
}
