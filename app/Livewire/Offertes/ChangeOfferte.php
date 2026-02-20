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
use App\Models\User;
use App\Rules\ZeroOrMinFifty;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Barryvdh\DomPDF\Facade\Pdf;

class ChangeOfferte extends Component
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
    public $kerndikte;

    public $project_naam;

    public $m2 = [];

    public $fillTotaleLengte = ['0'];
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
    public $offerte;

    public $marge;
    public $offerte_id;
    public $creator_user_id;
    public $creator_user;
    public $exsistingOfferteLines;
    public $saved = FALSE;
    public $priceRulePrice;
    public $requested_delivery_date;
    public $comment;



    public function mount($id) {
        if(Auth::user()->bedrijf_id == 0) {
            session()->flash('error', 'Uw account is niet gekoppeld aan een bedrijf. Hierdoor kunt u geen offertes plaatsen. Neem contact met rietpanel op om dit probleem te verhelpen.');
            return $this->redirect('/offertes', navigate: true);
        }
        $this->offerte_id = $id;
        $this->wandSupliers = Supliers::where('toepassing_wand', 1)->get();
        $this->dakSupliers = Supliers::where('toepassing_dak', 1)->get();
        $this->panelTypes = PanelType::whereIn('id', PriceRules::pluck('panel_type'))->get();
        $this->offerte = Offerte::where('id', $id)->first();
        $this->creator_user_id = $this->offerte->user_id;

        $this->creator_user = User::where('id', $this->creator_user_id)->first();

        $this->company = Company::where('id', $this->creator_user->bedrijf_id)->first();
        $this->companyDiscount = $this->company->discount;

        $this->exsistingOfferteLines = OfferteLines::where('offerte_id', $id)->get();

        $this->werkendeBreedte = $this->dakSupliers->first()->werkende_breedte;
        $this->brands = $this->dakSupliers;

        $this->klant_naam = $this->offerte->klantnaam;
        $this->referentie = $this->offerte->referentie;
        $this->aflever_straat = $this->offerte->aflever_straat;
        $this->aflever_postcode = $this->offerte->aflever_postcode;
        $this->aflever_land = $this->offerte->aflever_land;
        $this->aflever_plaats = $this->offerte->aflever_plaats;
        $this->intaker = $this->offerte->intaker;
        $this->rietkleur = $this->offerte->rietkleur;
        $this->toepassing = $this->offerte->toepassing;
        $this->merk_paneel = $this->offerte->merk_paneel;
        $this->kerndikte = $this->offerte->kerndikte;
        $this->discount = $this->offerte->discount;
        $this->project_naam = $this->offerte->project_naam;
        $this->marge = $this->offerte->marge;
        $this->comment = $this->offerte->comment;

        $this->priceRule = PanelType::where('name', $this->kerndikte)->first()->priceRule;
        $this->priceRulePrice = $this->priceRule->price;
        $this->requested_delivery_date = $this->offerte->requested_delivery_date;

        foreach($this->exsistingOfferteLines as $key => $exsistingOfferteLine) {
            $this->offerteLines[] = $key;
            $this->fillCb[$key] = $exsistingOfferteLine->fillCb;
            $this->fillTotaleLengte[$key] = $exsistingOfferteLine->fillTotaleLengte;
            $this->aantal[$key] = $exsistingOfferteLine->aantal;
            $this->m2[$key] = $exsistingOfferteLine->m2;
            $this->cb[$key] = $exsistingOfferteLine->fillCb;
            $this->totaleLengte[$key] = $exsistingOfferteLine->fillTotaleLengte;

        }

        if(Auth::user()->is_admin || !Auth::user()->is_architect) {
            return view('livewire.offertes.offertes');
        } else {
            session()->flash('error','U heeft geen rechten voor deze pagina');
            return $this->redirect('/dashboard', navigate: true);
        }
    }

    public function render()
    {
        return view('livewire.offertes.changeOfferte');
    }

    public function showLines() {
        dd($this->offerteLines);
    }

    public function updatePrice() {
        $this->priceRule = PanelType::where('name', $this->kerndikte)->first()->priceRule;
        $this->priceRulePrice = $this->priceRule->price;
        $this->kerndikte = $this->kerndikte;
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
        $this->fillTotaleLengte[] = '0';
        $this->aantal[] = '1';



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
        return [
            'klant_naam' => 'required',
            'referentie' => 'required',
            'project_naam' => 'required',
            'aflever_straat' => 'required',
            'aflever_postcode' => 'required',
            'aflever_plaats' => 'required',
            'aflever_land' => 'required',
            'intaker' => 'required',
            'discount' => 'required|min:0',
            'fillTotaleLengte.*' => 'required|numeric|min:500|max:14500',
            'aantal.*' => 'required|numeric|min:1',
            'requested_delivery_date' => 'required',

            'fillCb.*' => ['required', 'numeric', 'max:200', function ($attribute, $value, $fail) {
                if ($value != 0 && $value < 20) {
                    $fail("De CB moet 0 zijn (geen CB) of minimaal 20mm.");
                }
            },
                ],
            ];
    }



    public function messages(): array
    {
        return [
            'klant_naam.required' => __('messages.De klantnaam is een verplicht veld.'),
            'project_naam.required' => __('messages.De projectnaam is een verplicht veld.'),
            'referentie.required' => __('messages.De referentie is een verplicht veld.'),
            'aflever_straat.required' => __('messages.De straat is een verplicht veld.'),
            'aflever_postcode.required' => __('messages.De postcode is een verplicht veld.'),
            'aflever_plaats.required' => __('messages.De plaats is een verplicht veld.'),
            'aflever_land.required' => __('messages.Het land is een verplicht veld.'),
            'discount.required' => __('messages.Vul aub de korting in. Als u de klant geen korting geeft, vul dan 0 in.'),
            'discount.min' => __('messages.De korting kan niet lager dan 0 procent zijn'),
            'intaker.required' => __('messages.Vul aub uw naam in.'),
            'totaleLengte.*.min' => __('messages.De lengte moet mimimaal 500mm zijn.'),
            'totaleLengte.*.max' => __('messages.De lengte mag maximaal 14500mm zijn.'),
            'totaleLengte.*.required' => __('messages.De lengte is een verplicht veld.'),
            'aantal.*.min' => __('messages.Dit moet mimimaal 1 paneel zijn.'),
            'aantal.*.required' => __('messages.Het aantal panelen is een verplicht veld.'),
            'cb.*.max' => __('messages.De CB mag maximaal 200mm zijn.'),
            'cb.*.min' => __('messages.De CB moet minimaal 20mm zijn.'),
            'lb.*.min' => __('messages.De LB moet minimaal 20mm zijn.'),
            'lb.*.max' => __('messages.De LB mag maximaal 210mm zijn.'),
            'kerndikte' => __('messages.De kerndikte is een verplicht veld'),
            'requested_delivery_date.required' => __('messages.Dit is een verplicht veld.'),
        ];
    }

    public function saveOfferte() {
        $this->validate();

        Offerte::where('id', $this->offerte_id)->update([
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
            'user_id' => $this->creator_user_id,
            'marge' => $this->marge,
            'status' => 'In behandeling',
            'requested_delivery_date' => $this->requested_delivery_date,
            'comment' => $this->comment,
        ]);

        $offerte = Offerte::orderBy('id', 'desc')->first();
        OfferteLines::where('offerte_id', $this->offerte_id)->delete();

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
                    'user_id' => $this->creator_user_id,
                    'm2' => $m2
                ]);
            }

        $offerteLines = OfferteLines::where('offerte_id', $offerte->id)->get();

        Pdf::loadView('pdf.offerte',['offerte' => $offerte, 'offerteLines' => $offerteLines])->save(public_path('/storage/offertes/offerte-'.$offerte->offerte_id.'.pdf'));

//        Mail::to(env('MAIL_TO_ADDRESS'))->send(new sendOfferte($offerte));

        session()->flash('success', __('messages.De offerte is bewerkt.'));
        return $this->redirect('/offertes', navigate: true);
    }

    public function cancelChangeOfferte() {
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
