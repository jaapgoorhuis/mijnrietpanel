<?php

namespace App\Livewire\Companys;

use App\Models\Company;
use Livewire\Attributes\On;
use App\Models\Order;
use App\Models\PriceRules;
use App\Models\Subcontractors;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use function Spatie\LaravelPdf\Support\pdf;

class CreateCompanys extends Component
{
    public $companys;
    public $bedrijfsnaam;
    public $architect_bureau = 0;
    public $discount;
    public $reseller = 0;
    public $straat;
    public $postcode;
    public $plaats;
    public $bill_email;

    public $message;
    public $messageStraat;

    public $lang = 'nl';

    public bool $showRestoreCompanyModal = false;
    public ?Company $restoreCompany = null;


    public ?Company $inactiveCompany = null;

    public function updatedBedrijfsnaam($value) {

        $this->message = '';
        $search = trim(mb_strtolower($value));
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

    public function updatedStraat($value) {

        $this->messageStraat = '';
        $search = trim($value);
        if ($search === '') return;


        // Controleer of er een exacte match is als substring in de database
        $exists = Subcontractors::whereRaw('LOWER(street) = ?', [mb_strtolower($search)])
            ->exists();

        $subcontractor = Subcontractors::whereRaw('LOWER(street) = ?', [mb_strtolower($search)])
            ->first();

        if ($exists) {
            if($subcontractor) {
                $headCompany = Company::where('id', $subcontractor->company_id)->first();
                $this->messageStraat = 'Let op: Onderaannemer ' . $subcontractor->name . ' van ' . $headCompany->bedrijfsnaam . '. heeft hetzelfde adres.';
            }
        }
    }

    public function render()
    {
        if(Auth::user()->is_admin) {
            $this->companys = Company::get();
            return view('livewire.companys.createCompanys');
        } else {
            return $this->redirect('/dashboard', navigate: true);
        }
    }

    public function cancelAddCompany() {
        return $this->redirect('/companys', navigate: true);
    }




    protected $rules = [
        'bedrijfsnaam' => 'required|unique:companys,bedrijfsnaam',
        'discount' => 'required',
        'architect_bureau' => 'required',
        'straat' => 'required',
        'plaats' => 'required',
        'postcode' => 'required',
        'bill_email' => 'nullable|email',


    ];

    public function messages(): array
    {
        return [
            'bedrijfsnaam.required' => 'De bedrijfsnaam is een verplicht veld.',
            'bedrijfsnaam.unique' => 'Er bestaat al een bedrijf met deze naam.',
            'discount.required'=> 'Vul een korting in. Als het bedrijf geen korting krijgt vul dan 0 in.',
            'architect_bureau.required'=> 'Vul in of het bedrijf een architecten bureau is.',
            'plaats.required' => 'De plaats is een verplicht veld.',
            'straat.required' => 'De straat is een verplicht veld.',
            'postcode.required' => 'De postcode is een verplicht veld.',
            'bill_email.email'=> 'Dit moet een geldig emailadres zijn',

        ];
    }
    public function createCompany() {

        $inactiveCompany = Company::where('bedrijfsnaam', $this->bedrijfsnaam)
            ->where('is_active', 0)
            ->first();

        if ($inactiveCompany) {

            $this->restoreCompany = $inactiveCompany;
            $this->showRestoreCompanyModal = true;

            return;
        }

        try {
            $this->validate();
        } catch (ValidationException $e) {
            Log::error('CreateCompany validatie mislukt', [
                'errors' => $e->errors(),
                'data' => $this->all(), // of alleen de velden die je wilt loggen
            ]);

            throw $e; // zodat Livewire de validatiefouten gewoon blijft tonen
        }


        Company::create([
            'bedrijfsnaam' => $this->bedrijfsnaam,
            'discount' => $this->discount,
            'is_architect_bureau' => $this->architect_bureau,
            'is_reseller' => 0,
            'straat' => $this->straat,
            'postcode' => $this->postcode,
            'plaats' => $this->plaats,
            'bill_email' => $this->bill_email,

        ]);

//        $companyId = Company::orderBy('id', 'desc')->first();
//
//
//
//        $pricerules = PriceRules::where('reseller', 0)->get();
//
//        if($this->reseller) {
//            foreach ($pricerules as $pricerule) {
//                PriceRules::create([
//                    'rule_name' => $pricerule->rule_name,
//                    'panel_type' => $pricerule->panel_type,
//                    'price' => $pricerule->price,
//                    'company_id' => $companyId->id,
//                    'reseller' => 1,
//
//                ]);
//            }
//        }

        session()->flash('success','Het bedrijf is aangemaakt');
        return $this->redirect('/companys', navigate: true);
    }

    public function restoreInactiveCompany()
    {
        if (! $this->restoreCompany) {
            return;
        }

        // Bedrijf herstellen
        Company::where('id', $this->restoreCompany->id)
            ->update(['is_active' => true]);

        // Prijsregels herstellen
        PriceRules::where('company_id', $this->restoreCompany->id)
            ->update(['is_active' => true]);

        // Gebruikers herstellen
        User::where('bedrijf_id', $this->restoreCompany->id)
            ->update(['is_active' => true]);

        $this->showRestoreCompanyModal = false;

        session()->flash('success', 'Het bedrijf is succesvol hersteld.');

        return $this->redirect('/companys', navigate: true);
    }

}
