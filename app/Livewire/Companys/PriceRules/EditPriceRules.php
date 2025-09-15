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

class EditPriceRules extends Component
{
    public $user;
    public $company_id;

    public $panel_brands;
    public $panel_types;
    public $panel_looks;
    public $panel_applications;

    public $panel_brand;
    public $panel_type;
    public $panel_look;
    public $toepassing;
    public $panel_price;
    public $rule_name;

    public $priceRule;
    public $priceRuleId;


    public function mount($id) {

        $this->priceRuleId = $id;
        $this->priceRule = \App\Models\PriceRules::where('id', $id)->first();
        $this->panel_types = PanelType::get();
        $this->panel_type = $this->priceRule->panel_type;
        $this->panel_price = $this->priceRule->price;
        $this->rule_name = $this->priceRule->rule_name;

    }
    public function render()
    {
        if(Auth::user()->is_admin) {
            return view('livewire.companys.pricerules.editPriceRules');
        } else {

            return $this->redirect('/dashboard', navigate: true);
        }
    }

    public function rules(): array
    {
        return [
            'rule_name' => [
                'required',
                Rule::unique('price_rules', 'rule_name')->ignore($this->priceRuleId)
            ],
            'panel_type' => [
                'required',
                Rule::unique('price_rules', 'panel_type')->ignore($this->priceRuleId)
            ],
            'panel_price' => 'required'
        ];
    }

    public function messages(): array
    {
        return [
            'rule_name.required' => 'De regelnaam is een verplicht veld.',
            'rule_name.unique' => 'Er bestaat al een prijsregel met deze naam.',
            'panel_price.required' => 'De prijs is een verplicht veld.',
            'panel_type.unique' => 'Er bestaat al een regel voor deze dikte.',
        ];
    }

    public function updatePriceRule() {

        $this->validate($this->rules());


        \App\Models\PriceRules::where('id', $this->priceRuleId)->update([
            'rule_name' => $this->rule_name,
            'panel_type' => $this->panel_type,
            'price' => $this->panel_price,
        ]);


        session()->flash('success','De prijsregel is aangepast');

        return $this->redirect('/companys/pricerules', navigate: true);
    }

    public function cancelEditPriceRule() {
        return $this->redirect('/companys/pricerules', navigate: true);
    }
}
