<?php

namespace App\Livewire\Supliers;

use App\Mail\sendUpdatedUser;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class Supliers extends Component
{
    public $supliers;


    public function render()
    {
        if(Auth::user()->is_admin) {

            $this->supliers = \App\Models\Supliers::orderBy('id', 'asc')->get();
            return view('livewire.supliers.supliers');
        } else {
            return $this->redirect('/dashboard', navigate: true);
        }
    }

    public function newSuplier() {
        return $this->redirect('/supliers/create', navigate: true);
    }

    public function editSuplier($id) {
        return $this->redirect('/supliers/edit/'.$id, navigate: true);
    }

    public function removeSuplier($id) {
        if(Auth::user()->is_admin) {
            return $this->redirect('/supliers/remove/' . $id, navigate: true);
        }
        else {
            return $this->redirect('/dashboard', navigate: true);
        }
    }
}
