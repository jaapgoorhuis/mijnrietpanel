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
    public $companyPriceRules;
    public $company_id;

    public $company;


    public function render()
    {
        $this->priceRules = \App\Models\PriceRules::where('company_id', 0)->where('reseller', 0)->get();

        $companyid = Auth::user()->companys->id;

        $this->companyPriceRules = \App\Models\PriceRules::where('company_id', $companyid)->get();
        return view('livewire.companys.pricerules.priceRules');
    }

    public function newRule() {
        return $this->redirect('/companys/pricerules/create', navigate: true);
    }

    public function editPriceRule($id) {
        return $this->redirect('/companys/pricerules/edit/'.$id, navigate: true);
    }

    public function editResellerPriceRule($id,$id2) {
        return $this->redirect('/company/'.$id2.'/pricerules/edit/'.$id, navigate: true);
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
