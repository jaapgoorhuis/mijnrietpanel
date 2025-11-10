<?php

namespace App\Livewire\Companys;

use App\Models\Company;
use App\Models\Order;
use App\Models\PriceRules;
use App\Models\Subcontractors;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;
use function Spatie\LaravelPdf\Support\pdf;

class EditCompanys extends Component
{
    public $company;
    public $bedrijfsnaam;
    public $discount;
    public $reseller;

    public $straat;
    public $postcode;
    public $plaats;

    public $message;

    public function mount($id) {
        $this->company = Company::where('id', $id)->first();

        $this->bedrijfsnaam = $this->company->bedrijfsnaam;
        $this->discount = $this->company->discount;
        $this->reseller = $this->company->is_reseller;
        $this->straat = $this->company->straat;
        $this->postcode = $this->company->postcode;
        $this->plaats = $this->company->plaats;
    }

    public function render()
    {
        if(Auth::user()->is_admin) {

            return view('livewire.companys.editCompanys');
        } else {
            return $this->redirect('/dashboard', navigate: true);
        }
    }

    public function filterSubcontractors() {

        $this->message = '';
        $search = trim(mb_strtolower($this->bedrijfsnaam));
        if ($search === '') return;


        // Controleer of er een exacte match is als substring in de database
        $exists = Subcontractors::whereRaw('LOWER(name) LIKE ?', ["%{$search}%"])
            ->exists();

        $subcontractor = Subcontractors::whereRaw('LOWER(name) LIKE ?', ["%{$search}%"])
            ->first();

        if ($exists) {
            if($subcontractor) {
                $headCompany = Company::where('id', $subcontractor->company_id)->first();
                $this->message = 'Let op: Het bedrijf ' . $subcontractor->name . ' is een onderaannemer van het bedrijf ' . $headCompany->bedrijfsnaam . '. Je kunt het bedrijf alsnog toevoegen.';
            }
        }
    }

    public function cancelEditCompany() {
        return $this->redirect('/companys', navigate: true);
    }


    public function rules(): array
    {
        return [
            'bedrijfsnaam' => [
                'required',
                Rule::unique('companys', 'bedrijfsnaam')->ignore($this->company->id)
            ],
            'discount' => 'required',
            'straat' => 'required',
            'plaats' => 'required',
            'postcode' => 'required'
        ];

    }

    public function messages(): array
    {
        return [
            'bedrijfsnaam.required' => 'De bedrijfsnaam is een verplicht veld.',
            'plaats.required' => 'De plaats is een verplicht veld.',
            'straat.required' => 'De straat is een verplicht veld.',
            'postcode.required' => 'De postcode is een verplicht veld.',
            'discount.required' => 'Vul de korting voor het bedrijf in. Als het bedrijf geen korting heeft vul dan 0 in.',

        ];
    }

    public function updateCompany($id) {
        $this->validate($this->rules());

        Company::where('id', $id)->update([
            'bedrijfsnaam' => $this->bedrijfsnaam,
            'discount' => $this->discount,
            'is_reseller' => $this->reseller,
            'straat' => $this->straat,
            'postcode' => $this->postcode,
            'plaats' => $this->plaats,
        ]);

        $pricerules = PriceRules::where('reseller', 0)->get();
        $existingPriceRules = PriceRules::where('company_id', $this->company->id)->get();

        if($this->reseller) {
            if(!count($existingPriceRules)) {
                foreach ($pricerules as $pricerule) {
                    PriceRules::create([
                        'rule_name' => $pricerule->rule_name,
                        'panel_type' => $pricerule->panel_type,
                        'price' => $pricerule->price,
                        'company_id' => $this->company->id,
                        'reseller' => 1,
                    ]);
                }
            }
        } else {
            $pricerules = PriceRules::where('company_id', $this->company->id)->get();
            foreach($pricerules as $rule) {
                $rule->delete();
            }
        }

        session()->flash('success','Het bedrijf is aangepast');
        return $this->redirect('/companys', navigate: true);
    }

}
