<?php

namespace App\Livewire\Offertes;

use App\Mail\sendOfferte;
use App\Mail\sendOrder;
use App\Models\Application;
use App\Models\Company;
use App\Models\Offerte;
use App\Models\OfferteLines;
use App\Models\Order;
use App\Models\OrderLines;
use App\Models\OrderTemplate;
use App\Models\PanelBrand;
use App\Models\PanelLook;
use App\Models\PanelType;
use App\Models\PriceRules;
use App\Models\Supliers;
use App\Rules\ZeroOrMinFifty;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Barryvdh\DomPDF\Facade\Pdf;

class CreateOffertes extends Component
{

    public $klant_naam;
    public $referentie;
    public $aflever_straat;
    public $aflever_postcode;
    public $aflever_plaats;
    public $aflever_land;

    public $intaker;

    public $rietkleur = 'Old look';
    public $toepassing = 'Dak';
    public $merk_paneel;
    public $aantal = [];
    public $kerndikte = '';

    public $project_naam;

    public $m2 = [];

    public $fillTotaleLengte = [''];
    public $fillCb = ['0'];
    public $fillLb = ['0'];

    public $lb = [];
    public $cb = [];
    public $totaleLengte = [];

    public $panelBrands;
    public $panelTypes;
    public $panelApplications;
    public $panelLooks;
    public $discount = 0;


    public $brands = [];


    public $offerteLines = [];
    public $wandSupliers;
    public $dakSupliers;

    public $werkendeBreedte;
    public $offerteLineValues = [];
    public $company;
    public $companyDiscount;
    public $priceRule;

    public $marge;
    public $priceRulePrice;
    public $saved = FALSE;

    public function mount() {
        if(Auth::user()->bedrijf_id == 0) {
            session()->flash('error', 'Uw account is niet gekoppeld aan een bedrijf. Hierdoor kunt u geen offertes plaatsen. Neem contact met rietpanel op om dit probleem te verhelpen.');
            return $this->redirect('/offertes', navigate: true);
        }
        $this->wandSupliers = Supliers::where('toepassing_wand', 1)->get();
        $this->dakSupliers = Supliers::where('toepassing_dak', 1)->get();
        $this->panelTypes = PanelType::whereIn('id', PriceRules::pluck('panel_type'))->get();


        $this->company = Company::where('id', Auth::user()->bedrijf_id)->first();
        $this->companyDiscount = $this->company->discount;

        $this->priceRulePrice = 0;
        $this->merk_paneel = $this->dakSupliers->first()->name;
        $this->werkendeBreedte = $this->dakSupliers->first()->werkende_breedte;
        $this->brands = $this->dakSupliers;


        if(Auth::user()->is_admin || !Auth::user()->is_architect) {
            return view('livewire.offertes.offertes');
        } else {
            session()->flash('error','U heeft geen rechten voor deze pagina');
            return $this->redirect('/dashboard', navigate: true);
        }

    }

    public function render()
    {


        return view('livewire.offertes.createOfferte');
    }

    public function updatePrice() {
        if($this->kerndikte != '') {
            $this->priceRule = PanelType::where('name', $this->kerndikte)->first()->priceRule;
            $this->priceRulePrice = $this->priceRule->price;
            $this->kerndikte = $this->kerndikte;
        } else {
            $this->priceRulePrice = 0;
        }
    }


    public function updateCb($index) {

        $this->cb[$index] = $this->fillCb[$index];
        if($this->fillCb[$index] == '') {
            $this->cb[$index] = '0';
        }
    }

    public function updateLb($index) {
        $this->lb[$index] = $this->fillLb[$index];
        if($this->fillLb[$index] == '') {
            $this->lb[$index] = '0';
        }
    }

    public function updateTotaleLengte($index) {
        $this->updateM2($index);
        $this->totaleLengte[$index] = $this->fillTotaleLengte[$index];
        if($this->fillTotaleLengte[$index] == '') {
            $this->totaleLengte[$index] = '0';
        }
    }

    public function updateBrands() {


        if($this->toepassing == 'Wand') {

            $this->brands = $this->wandSupliers;

        }else if($this->toepassing == 'Dak') {
            $this->merk_paneel = $this->dakSupliers->first()->name;
            $this->brands = $this->dakSupliers;
        }


    }

    public function addOfferteLine() {
        $this->offerteLines[] = '';
        $this->fillCb[] = '0';
        $this->cb[] = '0';
        $this->m2[] = '0';
        $this->lb[] = '0';
        $this->fillLb[] = '0';
        $this->totaleLengte[] = '0';
        $this->fillTotaleLengte[] = '';
        $this->aantal[] = '';

    }

    public function removeOfferteLine($index) {
        unset($this->offerteLines[$index]);
        unset($this->totaleLengte[$index]);
        unset($this->aantal[$index]);
        unset($this->lb[$index]);
        unset($this->cb[$index]);
        $this->offerteLines = array_values($this->offerteLines);
        $this->totaleLengte = array_values($this->totaleLengte);
        $this->aantal = array_values($this->aantal);
        $this->lb = array_values($this->lb);
        $this->cb = array_values($this->cb);
    }

    public function rules()
    {
        $rules = [
            'klant_naam' => 'required',
            'referentie' => 'required',
            'project_naam' => 'required',
            'aflever_straat' => 'required',
            'aflever_postcode' => 'required',
            'aflever_plaats' => 'required',
            'aflever_land' => 'required',
            'intaker' => 'required',
            'discount' => 'required|min:0',
            'totaleLengte.*' => 'required|numeric|min:500',
            'aantal.*' => 'required|numeric|min:1',
            'kerndikte' => 'required',

            'cb.*' => [
                'required', 'numeric', 'max:200',
                function ($attribute, $value, $fail) {
                    if ($value != 0 && $value < 20) {
                        $fail("De waarde van $attribute moet 0 zijn of minimaal 20mm.");
                    }
                },
            ],
        ];

        // Conditioneel extra rule toevoegen op lb.*
        if (Auth::user()->is_admin == 0 && Auth::user()->company->is_reseller == 0) {
            $rules['marge'] = 'required';
        }

        return $rules;
    }



    public function messages(): array
    {
        return [
            'klant_naam.required' => 'De klantnaam is een verplicht veld.',
            'project_naam.required' => 'De projectnaam is een verplicht veld.',
            'referentie.required' => 'De referentie is een verplicht veld.',
            'aflever_straat.required' => 'De straat is een verplicht veld.',
            'aflever_postcode.required' => 'De postcode is een verplicht veld.',
            'aflever_plaats.required' => 'De plaats is een verplicht veld.',
            'aflever_land.required' => 'Het land is een verplicht veld.',
            'intaker.required' => 'Vul aub uw naam in.',
            'discount.required' => 'Vul aub de korting in. Als u de klant geen korting geeft, vul dan 0 in.',
            'discount.min' => 'De korting kan niet lager dan 0 procent zijn.',
            'totaleLengte.*.min' => 'De lengte moet mimimaal 500mm zijn.',
            'aantal.*.min' => 'Dit moet mimimaal 1 paneel zijn.',
            'aantal.*.required' => 'Het aantal panelen is een verplicht veld.',
            'cb.*.max' => 'De CB mag maximaal 200mm zijn.',
            'lb.*.max' => 'De LB mag maximaal 210mm zijn.',
            'marge' => 'De marge is een verplicht veld',
            'kerndikte' => 'De kerndikte is een verplicht veld',
        ];
    }

    public function saveOfferte() {

        $this->validate();

        $latestOfferte = Offerte::orderBy('id', 'desc')->first();

        if($latestOfferte) {
            $currentYear = date('y');
            if(str_starts_with($latestOfferte->offerte_id, $currentYear)) {
                $offerteId = $latestOfferte->offerte_id + 1;
            } else {
                $offerteId = $currentYear.'0600';
            }

        } else {
            $offerteId = 250600;
        }
        Offerte::create([
            'klantnaam' => $this->klant_naam,
            'referentie' => $this->referentie,
            'aflever_straat' => $this->aflever_straat,
            'aflever_postcode' => $this->aflever_postcode,
            'aflever_land' => $this->aflever_land,
            'aflever_plaats' => $this->aflever_plaats,
            'intaker' => $this->intaker,
            'discount' => $this->discount,
            'merk_paneel' => $this->merk_paneel,
            'rietkleur' => $this->rietkleur,
            'toepassing' => $this->toepassing,
            'kerndikte' => $this->kerndikte,
            'project_naam' => $this->project_naam,
            'user_id' => Auth::user()->id,
            'marge' => $this->marge,
            'status' => 'In behandeling',
            'offerte_id' => $offerteId
        ]);

        $offerte = Offerte::orderBy('id', 'desc')->first();

        foreach($this->offerteLines as $index => $key) {

                $fillCb = array_key_exists($index, $this->fillCb) ? $this->fillCb[$index] : '0';
                $fillLb = array_key_exists($index, $this->fillLb) ? $this->fillLb[$index] : '0';
                $fillTotaleLengte = array_key_exists($index, $this->fillTotaleLengte) ? $this->fillTotaleLengte[$index] : '0';
                $aantal = array_key_exists($index, $this->aantal) ? $this->aantal[$index] : '0';
                $m2 = array_key_exists($index, $this->m2) ? $this->m2[$index] : '0';

                OfferteLines::create([
                    'offerte_id' => $offerte->id,
                    'fillCb' => $fillCb,
                    'fillLb' => $fillLb,
                    'fillTotaleLengte' => $fillTotaleLengte,
                    'aantal' => $aantal,
                    'user_id' => Auth::user()->id,
                    'm2' => $m2
                ]);
            }

        $offerteLines = OfferteLines::where('offerte_id', $offerte->id)->get();

        Pdf::loadView('pdf.offerte',['offerte' => $offerte, 'offerteLines' => $offerteLines])->save(public_path('/storage/offertes/offerte-'.$offerteId.'.pdf'));

//        Mail::to(env('MAIL_TO_ADDRESS'))->send(new sendOfferte($offerte));

        session()->flash('success','De offerte is aangemaakt.');
        return $this->redirect('/offertes', navigate: true);
    }

    public function cancelCreateOfferte() {
        return $this->redirect('/offertes', navigate: true);
    }

    public function updateM2($index) {

        foreach($this->brands as $brands) {
            if($brands->name == $this->merk_paneel) {
                $this->werkendeBreedte = $brands->werkende_breedte;
            }
        }

           $lengtePaneel = (int)$this->fillTotaleLengte[$index];

           $werkendeBreedteM = $this->werkendeBreedte / 1000;
           $lengtePaneelM = $lengtePaneel / 1000;


           if($lengtePaneel == '0' || $this->werkendeBreedte == '0') {
               $this->m2[$index] = 0;
           }
           else {
               $this->m2[$index] = round($lengtePaneelM * $werkendeBreedteM * intval($this->aantal[$index]),2);
           }
    }
}
