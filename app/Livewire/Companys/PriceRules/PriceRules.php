<?php

namespace App\Livewire\Companys\PriceRules;

use App\Mail\sendUpdatedUser;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class PriceRules extends Component
{
    public $user;
    public $priceRules;
    public $company_id;

    public $company;


    public function render()
    {
        if(Auth::user()->is_admin) {

            $this->priceRules = \App\Models\PriceRules::get();
            return view('livewire.companys.pricerules.priceRules');
        } else {
            return $this->redirect('/dashboard', navigate: true);
        }
    }

    public function newRule() {
        return $this->redirect('/companys/pricerules/create', navigate: true);
    }

    public function editPriceRule($id) {
        return $this->redirect('/companys/pricerules/edit/'.$id, navigate: true);
    }

    public function removePriceRule($id) {
        if(Auth::user()->is_admin) {
            return $this->redirect('/companys/pricerules/remove/' . $id, navigate: true);
        }
        else {
            return $this->redirect('/dashboard', navigate: true);
        }
    }
}
