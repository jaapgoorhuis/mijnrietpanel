<?php

namespace App\Livewire\AccountRequests;

use App\Models\Company;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use function Spatie\LaravelPdf\Support\pdf;

class AccountRequests extends Component
{
    public $users;

    public $company_id;

    public $company;

    public function render()
    {
        if(Auth::user()->is_admin) {
            $this->users = User::where('bedrijf_id', 0)->get();
            return view('livewire.accountRequests.accountRequests');
        } else {
            return $this->redirect('/dashboard', navigate: true);
        }
    }

    public function editAccountRequest($id) {
        return $this->redirect('accountrequests/edit/'.$id, navigate: true);
    }
}
