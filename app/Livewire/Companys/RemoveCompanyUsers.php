<?php

namespace App\Livewire\Companys;

use App\Mail\sendUpdatedUser;
use App\Models\Company;
use App\Models\PriceRules;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class RemoveCompanyUsers extends Component
{
    public $company;

    public $userid;
    public $companyid;

    public $user;
    public function mount($id,$slug) {

        $this->userid = $id;
        $this->companyid = $slug;
        $this->user = User::where('id',$this->userid)->first();
        $this->company = Company::where('id', $this->companyid)->first();

    }
    public function render()
    {
        if(Auth::user()->is_admin) {
            return view('livewire.companys.removeCompanyUser');
        } else {
            return $this->redirect('/dashboard', navigate: true);
        }
    }
    public function cancelDeleteUser() {
        return $this->redirect('/companys/'.$this->companyid.'/users', navigate: true);
    }

    public function deleteUser($id)
    {
        // Check: probeert gebruiker zichzelf te verwijderen?
        if (Auth::id() == $id) {
            session()->flash('error', 'Je kunt jezelf niet verwijderen.');
            return;
        }

        User::where('id', $id)->update([
            'is_removed' => 1
        ]);

        session()->flash('success', 'De gebruiker is verwijderd.');

        return $this->redirect('/companys/' . $this->companyid . '/users', navigate: true);
    }

}
