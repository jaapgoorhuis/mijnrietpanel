<?php

namespace App\Livewire\Companys\Subcontractors;

use App\Mail\sendUpdatedUser;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class RemoveSubcontractor extends Component
{
    public $subcontractor;

    public $company_id;

    public function mount($id,$slug) {
        $this->company_id = $slug;
        $this->subcontractor = \App\Models\Subcontractors::where('id', $id)->first();
    }
    public function render()
    {
        if(Auth::user()->is_admin) {
            return view('livewire.companys.subcontractors.removeSubcontractor');
        } else {
            return $this->redirect('/dashboard', navigate: true);
        }
    }
    public function cancelDeleteSubcontractor() {
        return $this->redirect('/companys/'.$this->company_id.'/subcontractors', navigate: true);
    }

    public function deleteSubcontractor($id) {
        session()->flash('success','De onderaannemer is verwijderd');

        $subcontractor = \App\Models\Subcontractors::where('id', $id)->first();

        $subcontractor->delete();

        return $this->redirect('/companys/'.$this->company_id.'/subcontractors', navigate: true);
    }

}
