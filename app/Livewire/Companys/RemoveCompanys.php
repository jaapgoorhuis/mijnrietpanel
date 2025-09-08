<?php

namespace App\Livewire\Companys;

use App\Mail\sendUpdatedUser;
use App\Models\Company;
use App\Models\PriceRules;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class RemoveCompanys extends Component
{
    public $company;


    public function mount($id) {

        $this->company = Company::where('id', $id)->first();

    }
    public function render()
    {
        if(Auth::user()->is_admin) {
            return view('livewire.companys.removeCompanys');
        } else {
            return $this->redirect('/dashboard', navigate: true);
        }
    }
    public function cancelDeleteCompany() {
        return $this->redirect('/companys', navigate: true);
    }

    public function deleteCompany($id) {
        session()->flash('success','Het bedrijf is verwijderd. Alle onderliggende gebruikers zijn verplaatst naar account aanvragen. Alle prijsregels zijn verwijderd.' );

        Company::where('id', $id)->delete();
        PriceRules::where('company_id', $id)->delete();

        $users = User::where('bedrijf_id', $id)->get();

        foreach($users as $user) {
            User::where('id', $user->id)->update([
                'company_id' => '0'
            ]);
        }

        return $this->redirect('/companys/', navigate: true);
    }

}
