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
    public $originalRuleName;
    public $originalPanelType;


    public function mount($id) {

        $this->priceRuleId = $id;
        $this->priceRule = \App\Models\PriceRules::where('id', $id)->first();
        $this->panel_types = PanelType::get();
        $this->panel_type = $this->priceRule->panel_type;
        $this->panel_price = $this->priceRule->price;
        $this->rule_name = $this->priceRule->rule_name;
        $this->originalRuleName = $this->rule_name;
        $this->originalPanelType = $this->panel_type;

    }
    public function render()
    {
        if(Auth::user()->is_admin) {
            return view('livewire.companys.pricerules.editPriceRules');
        } else {

            return $this->redirect('/dashboard', navigate: true);
        }
    }


    public function rules()
    {
        return [
            'rule_name' => $this->rule_name === $this->originalRuleName
                ? ['required'] // niet uniek checken als niet veranderd
                : [
                    'required',
                    Rule::unique('price_rules', 'rule_name')
                        ->where(fn($query) => $query->where('company_id', 0))
                        ->ignore($this->priceRuleId)
                ],

            'panel_type' => $this->panel_type === $this->originalPanelType || $this->panel_type == 0
                ? ['required'] // geen unique check als niet veranderd of type 0
                : [
                    'required',
                    Rule::unique('price_rules')
                        ->where(fn($query) => $query
                            ->where('company_id', 0)
                            ->where('panel_type', $this->panel_type)
                        )
                        ->ignore($this->priceRuleId)
                ],

            'panel_price' => 'required',
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

        if($this->panel_type == 0) {
            \App\Models\PriceRules::where('id', $this->priceRuleId)->update([
                'rule_name' => $this->rule_name,
                'panel_type' => $this->panel_type,
                'price' => $this->panel_price,
            ]);
        }

        else {

            \App\Models\PriceRules::where('id', $this->priceRuleId)->update([
                'rule_name' => $this->rule_name,
                'panel_type' => $this->panel_type,
                'price' => $this->panel_price,
            ]);

            $companyPriceRules = \App\Models\PriceRules::where('panel_type', $this->panel_type)->get();

            foreach ($companyPriceRules as $companyPriceRule) {
                $companyPriceRule->update([
                    'rule_name' => $this->rule_name
                ]);
            }
        }


        session()->flash('success','De prijsregel is aangepast');

        return $this->redirect('/companys/pricerules', navigate: true);
    }

    public function cancelEditPriceRule() {
        return $this->redirect('/companys/pricerules', navigate: true);
    }
}
