<?php

namespace App\Livewire\Companys\Subcontractors;

use App\Mail\sendUpdatedUser;
use App\Models\Company;
use App\Models\Subcontractors;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class Subcontractor extends Component
{
    public $subcontractors;

    public $company_id;

    public $company;

    public function mount($slug) {


        $this->company_id = $slug;
    }

    public function render()
    {
        $this->subcontractors = Subcontractors::where('company_id', $this->company_id)->get();
        $this->company = Company::where('id', $this->company_id)->first();
        return view('livewire.companys.subcontractors.subcontractors');
    }


    public function editSubcontractor($id) {
        return $this->redirect('/companys/'.$this->company_id.'/subcontractors/edit/'.$id, navigate: true);
    }

    public function newSubcontractor() {
        return $this->redirect('/companys/'.$this->company_id.'/subcontractors/create/', navigate: true);
    }



    public function removeSubcontractor($id) {
        if(Auth::user()->is_admin) {
            return $this->redirect('/companys/'.$this->company_id.'/subcontractors/remove/' . $id, navigate: true);
        }
        else {
            return $this->redirect('/dashboard', navigate: true);
        }
    }
}
