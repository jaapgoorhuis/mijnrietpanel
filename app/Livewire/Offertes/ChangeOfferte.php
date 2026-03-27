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

    public $selectedPanelOption =[];
    public $panelValues =[];
    public $vrijeruimtePrice;
    public $laybackPrice;
    public $nokafschuiningPrice;
    public $panelImages = [];

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
            $this->panelValues[$key] = [
                1 => $exsistingOfferteLine->lb ?? 0,
                2 => $exsistingOfferteLine->fillCb ?? 0,
                3 => $exsistingOfferteLine->nokafschuining ?? 0,
                '4_1' => $exsistingOfferteLine->vrije_ruimte_1 ?? 0,
                '4_2' => $exsistingOfferteLine->vrije_ruimte_2 ?? 0,
            ];

            // ✅ Vul selectedPanelOption op basis van welke waarden groter dan 0 zijn
            $this->selectedPanelOption[$key] = [];
            if($exsistingOfferteLine->lb > 0) $this->selectedPanelOption[$key][] = 1;
            if($exsistingOfferteLine->fillCb > 0) $this->selectedPanelOption[$key][] = 2;
            if($exsistingOfferteLine->nokafschuining > 0) $this->selectedPanelOption[$key][] = 3;
            if($exsistingOfferteLine->vrije_ruimte_1 > 0 || $exsistingOfferteLine->vrije_ruimte_2 > 0) $this->selectedPanelOption[$key][] = 4;

            if(empty($this->selectedPanelOption[$key])) {
                $this->panelImages[$key] = '/storage/images/rietpanel/paneel.png';
            } else {
                $options = $this->selectedPanelOption[$key];
                sort($options);
                $keyString = implode('-', $options);
                $this->panelImages[$key] = "/storage/images/rietpanel/paneel-$keyString.png";
            }
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
        $this->nokafschuiningPrice = \App\Models\Surcharges::where('rule', 'Nokafschuining')->first()->price;
        $this->laybackPrice = \App\Models\Surcharges::where('rule', 'Layback')->first()->price;
        $this->vrijeruimtePrice = \App\Models\Surcharges::where('rule', 'Vrije ruimte')->first()->price;
        return view('livewire.offertes.changeOfferte');
    }

    public function updateSelectedPanelOption($index)
    {
        $options = $this->selectedPanelOption[$index] ?? [];

        if (empty($options)) {
            $this->panelImages[$index] = '/storage/images/rietpanel/paneel.png';
            return;
        }


        sort($options);
        $key = implode('-', $options);

        if (in_array(1, $options)) {
            $this->panelValues[$index][1] = 20;
        }

        if (in_array(2, $options)) {
            $this->panelValues[$index][2] = 20;
        }


        $this->panelImages[$index] = "/storage/images/rietpanel/paneel-$key.png";
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
        $this->panelValues[] = [
            1 => 20,
            2 => 20,
            3=> 0,
            '4_1' => 0,
            '4_2' => 0
        ];
        $this->panelImages[] = '/storage/images/rietpanel/paneel.png';
        $this->selectedPanelOption[] = [];


    }

    public function removeOfferteLine($index) {
        unset($this->offerteLines[$index]);
        unset($this->totaleLengte[$index]);
        unset($this->aantal[$index]);
        unset($this->lb[$index]);
        unset($this->cb[$index]);
        unset($this->selectedPanelOption[$index]);
        unset($this->panelImages[$index]);

        $this->offerteLines = array_values($this->offerteLines);
        $this->totaleLengte = array_values($this->totaleLengte);
        $this->aantal = array_values($this->aantal);
        $this->lb = array_values($this->lb);
        $this->cb = array_values($this->cb);
        $this->selectedPanelOption = array_values($this->selectedPanelOption);
        $this->panelImages = array_values($this->panelImages);
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
        if (in_array(1, array_merge(...$this->selectedPanelOption))) {
            $rules['panelValues.*.1'] = 'required|numeric';
        }

        if (in_array(2, array_merge(...$this->selectedPanelOption))) {
            $rules['panelValues.*.2'] = 'required|numeric';
        }

        if (in_array(3, array_merge(...$this->selectedPanelOption))) {
            $rules['panelValues.*.3'] = 'required|numeric|min:1';
        }

        if (in_array(4, array_merge(...$this->selectedPanelOption))) {
            // 4_1 moet > 0 zijn
            $rules['panelValues.*.4_1'] = 'required|numeric|min:1';

            // 4_2 moet > 0 zijn en mag niet de marge overschrijden
            $rules['panelValues.*.4_2'] = [
                'required',
                'numeric',
                'min:1', // ✅ waarde mag niet 0 zijn
                function ($attribute, $value, $fail) {
                    // $attribute = 'panelValues.0.4_2'
                    preg_match('/panelValues\.(\d+)\.4_2/', $attribute, $matches);
                    $index = $matches[1];

                    $totaal = $this->fillTotaleLengte[$index] ?? 0;

                    if (!$totaal) {
                        $fail(__('messages.Vul eerst de totale paneellengte in voor dit paneel'));
                    } else {
                        $marge = 300;
                        $max = $totaal - $marge;
                        $sum = ($this->panelValues[$index]['4_1'] ?? 0) + $value;

                        if ($sum > $max) {
                            $fail(__("messages.De som van 'Ruimte top tot vrije ruimte' + 'vrije ruimte' mag niet meer zijn dan ") . $max . "mm");
                        }
                    }
                },
            ];
        }
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
            'panelValues.*.3.min' =>  __('messages.Dit moet een getal hoger dan 0 zijn'),
            'panelValues.*.4_1.min' =>  __('messages.Dit moet een getal hoger dan 0 zijn'),
            'panelValues.*.4_2.min' =>  __('messages.Dit moet een getal hoger dan 0 zijn'),
            'panelValues.*.4_1.numeric' => 'Dit moet een getal zijn, hoger dan 0',
            'panelValues.*.4_2.numeric' => 'Dit moet een getal zijn, hoger dan 0',
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
                $selectedOptions = $this->selectedPanelOption[$index] ?? [];

                OfferteLines::create([
                    'offerte_id' => $offerte->id,
                    'fillLb' => $fillLb,
                    'fillTotaleLengte' => $fillTotaleLengte,
                    'aantal' => $aantal,
                    'user_id' => $this->creator_user_id,
                    'm2' => $m2,

                      // als optie niet geselecteerd is -> 0
                    'lb' => in_array(1, $selectedOptions) ? ($this->panelValues[$index][1] ?? 0) : 0,
                    'nokafschuining' => in_array(3, $selectedOptions) ? ($this->panelValues[$index][3] ?? 0) : 0,
                    'vrije_ruimte_1' => in_array(4, $selectedOptions) ? ($this->panelValues[$index]['4_1'] ?? 0) : 0,
                    'vrije_ruimte_2' => in_array(4, $selectedOptions) ? ($this->panelValues[$index]['4_2'] ?? 0) : 0,
                    'fillCb' => in_array(2, $selectedOptions) ? ($this->panelValues[$index][2] ?? 0) : 0,
                ]);
            }

        $offerteLines = OfferteLines::where('offerte_id', $offerte->id)->get();
        $showNokafschuining = $offerteLines->where('nokafschuining', '>', 0)->count() > 0;
        $showVrijeRuimte = $offerteLines->where('vrije_ruimte_2', '>', 0)->count() > 0;
        $showCb = $offerteLines->where('fillCb', '>', 0)->count() > 0;
        $showLb = $offerteLines->where('lb', '>', 0)->count() > 0;

        Pdf::loadView('pdf.offerte', ['offerte' => $offerte, 'offerteLines' => $offerteLines, 'showNokafschuining' => $showNokafschuining, 'showLb' => $showLb, 'showCb' => $showCb, 'showVrijeRuimte' => $showVrijeRuimte])->save(public_path('/storage/offertes/offerte-' . $offerteId . '.pdf'));


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
