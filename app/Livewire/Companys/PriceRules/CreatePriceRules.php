<?php

namespace App\Livewire\Companys\PriceRules;

use App\Mail\sendUpdatedUser;
use App\Models\Application;
use App\Models\PanelBrand;
use App\Models\PanelLook;
use App\Models\PanelType;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Livewire\Component;

class CreatePriceRules extends Component
{
    public $user;
    public $priceRules;
    public $company_id;

    public $panel_brands;
    public $panel_types;
    public $panel_looks;
    public $panel_applications;

    public $panel_brand = 1;
    public $panel_type = 1;
    public $panel_look = 1;
    public $toepassing = 1;
    public $panel_price;
    public $rule_name;


    public function mount() {

        $this->panel_types = PanelType::get();
    }
    public function render()
    {
        if(Auth::user()->is_admin) {
            return view('livewire.companys.pricerules.createPriceRules');
        } else {
            return $this->redirect('/dashboard', navigate: true);
        }
    }

    public function rules(): array
    {
        return [
            'rule_name' => 'required|unique:price_rules,rule_name',
            'panel_type' => 'required|unique:price_rules,panel_type',
            'panel_price' => 'required'
    ];}

    public function messages(): array
    {
        return [
            'rule_name.required' => 'De regelnaam is een verplicht veld.',
            'rule_name.unique' => 'Er bestaat al een prijsregel met deze naam.',
            'panel_price.required' => 'De prijs is een verplicht veld.',
            'panel_type.unique' => 'Er bestaat al een regel voor deze dikte.',
        ];
    }

    public function createPriceRule() {
        $this->validate($this->rules());

        \App\Models\PriceRules::create([
            'rule_name' => $this->rule_name,
            'panel_type' => $this->panel_type,
            'price' => $this->panel_price,
        ]);

        $companyPriceRules = \App\Models\PriceRules::where('company_id', '!=', 0)->get();

        foreach($companyPriceRules as $companyPriceRule) {
            $exists = \App\Models\PriceRules::where('company_id', $companyPriceRule->company_id)
                ->where('panel_type', $this->panel_type)
                ->exists();

            if (!$exists) {
                \App\Models\PriceRules::create([
                    'rule_name' => $this->rule_name,
                    'panel_type' => $this->panel_type,
                    'price' => $this->panel_price,
                    'company_id' => $companyPriceRule->company_id,
                    'reseller' => $companyPriceRule->reseller
                ]);
            }
        }


        return $this->redirect('/companys/pricerules', navigate: true);
    }

    public function cancelCreatePriceRule() {
        return $this->redirect('/companys/pricerules', navigate: true);
    }
}
