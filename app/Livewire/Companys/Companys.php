<?php

namespace App\Livewire\Companys;

use App\Models\Company;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use function Spatie\LaravelPdf\Support\pdf;

class Companys extends Component
{
    public $companys;
    public function render()
    {
        if(Auth::user()->is_admin) {
            $this->companys = Company::get();
            return view('livewire.companys.companys');
        } else {
            return $this->redirect('/dashboard', navigate: true);
        }
    }

    public function newCompany() {
        return $this->redirect('/companys/create', navigate: true);
    }

    public function companyUsers($id) {

        return $this->redirect('/companys/'.$id.'/users', navigate: true);
    }

    public function priceRules() {
        return $this->redirect('/companys/pricerules', navigate: true);
    }

    public function editCompany($id) {
        return $this->redirect('/companys/edit/'.$id, navigate: true);
    }

    public function subcontractors($id) {
        return $this->redirect('/companys/'.$id.'/subcontractors', navigate: true);
    }

    public function removeCompany($id){
        return $this->redirect('/companys/remove/'.$id, navigate: true);
    }
}
