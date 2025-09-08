<?php

namespace App\Livewire\Companys;

use App\Models\Company;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use function Spatie\LaravelPdf\Support\pdf;

class CompanyUsers extends Component
{
    public $users;

    public $company_id;

    public $company;
    public function mount($id) {
        $this->company_id = $id;

    }
    public function render()
    {
        if(Auth::user()->is_admin) {
            $this->users = User::where('bedrijf_id', $this->company_id)->get();
            $this->company = Company::where('id', $this->company_id)->first();
            return view('livewire.companys.companyUsers');
        } else {
            return $this->redirect('/dashboard', navigate: true);
        }
    }

    public function editUser($id) {
        return $this->redirect('/companys/'.$this->company_id.'/users/edit/'.$id, navigate: true);
    }
}
