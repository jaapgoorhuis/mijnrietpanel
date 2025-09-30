<?php

namespace App\Livewire\Supliers;

use App\Mail\sendUpdatedUser;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class RemoveSupliers extends Component
{
    public $suplier;


    public function mount($id) {
        $this->suplier = \App\Models\Supliers::where('id', $id)->first();

    }
    public function render()
    {
        if(Auth::user()->is_admin) {
            return view('livewire.supliers.removeSupliers');
        } else {
            return $this->redirect('/dashboard', navigate: true);
        }
    }
    public function cancelRemoveSuplier() {
        return $this->redirect('/supliers', navigate: true);
    }

    public function deleteSuplier($id) {
        session()->flash('success','De leverancier is verwijderd');

        \App\Models\Supliers::where('id', $id)->delete();
        return $this->redirect('/supliers', navigate: true);
    }

}
