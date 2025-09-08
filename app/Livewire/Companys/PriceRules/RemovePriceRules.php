<?php

namespace App\Livewire\Companys\PriceRules;

use App\Mail\sendUpdatedUser;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class RemovePriceRules extends Component
{
    public $priceRule;


    public function mount($id) {
        $this->priceRule = \App\Models\PriceRules::where('id', $id)->first();

    }
    public function render()
    {
        if(Auth::user()->is_admin) {
            return view('livewire.companys.pricerules.removePriceRules');
        } else {
            return $this->redirect('/dashboard', navigate: true);
        }
    }
    public function cancelRemovePriceRule() {
        return $this->redirect('/companys/pricerules', navigate: true);
    }

    public function deletePriceRule($id) {
        session()->flash('success','De prijsregel is verwijderd');

        \App\Models\PriceRules::where('id', $id)->delete();
        return $this->redirect('/companys/pricerules', navigate: true);
    }

}
