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

    public $locale;

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

    public $requested_delivery_date;

    public $comment;
    public $selectedPanelOption =[];
    public $panelValues =[];

    public $vrijeruimtePrice;
    public $laybackPrice;
    public $nokafschuiningPrice;

    public $panelImages = [];


    public function mount() {
        if(Auth::user()->bedrijf_id == 0) {
            session()->flash('error', 'Uw account is niet gekoppeld aan een bedrijf. Hierdoor kunt u geen offertes plaatsen. Neem contact met rietpanel op om dit probleem te verhelpen.');
            return $this->redirect('/offertes', navigate: true);
        }
        $this->wandSupliers = Supliers::where('toepassing_wand', 1)->get();
        $this->dakSupliers = Supliers::where('toepassing_dak', 1)->get();
        $this->panelTypes = PanelType::whereIn('id', PriceRules::pluck('panel_type'))->get();

        $this->locale = config('app.locale'); // leest APP_LOCALE uit .env

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

    public function updateSelectedPanelOption($index)
    {
        $options = $this->selectedPanelOption[$index] ?? [];

        if (empty($options)) {
            $this->panelImages[$index] = '/storage/images/rietpanel/paneel.png';
            return;
        }


        sort($options);
        $key = implode('-', $options);


        $this->panelImages[$index] = "/storage/images/rietpanel/paneel-$key.png";
    }


    public function updatePanelValues($key,$index)
    {
        $this->panelValues[$key][$index] = $this->panelValues[$key][$index];
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
        unset($this->fillTotaleLengte[$index]);
        unset($this->aantal[$index]);
        unset($this->lb[$index]);
        unset($this->cb[$index]);
        unset($this->selectedPanelOption[$index]);
        $this->offerteLines = array_values($this->offerteLines);
        $this->fillTotaleLengte = array_values($this->fillTotaleLengte);
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
            'aantal.*' => 'required|numeric|min:1',
            'kerndikte' => 'required',
            'requested_delivery_date' => 'required',

        ];


        // Conditioneel extra rule toevoegen op lb.*
        if (Auth::user()->is_admin == 0 && Auth::user()->company->is_reseller == 0) {
            $rules['marge'] = 'required';
        }
        foreach ($this->selectedPanelOption as $index => $options) {

            if (in_array(1, $options)) {
                $rules["panelValues.$index.1"] = 'required|numeric';
            }

            if (in_array(2, $options)) {
                $rules["panelValues.$index.2"] = 'required|numeric';
            }

            if (in_array(3, $options)) {
                $rules["panelValues.$index.3"] = 'required|numeric|min:1|max:60';
            }

            if (in_array(4, $options)) {

                // 4_1 moet > 0 zijn
                $rules["panelValues.$index.4_1"] = 'required|numeric|min:300';

                // 4_2 validation
                $rules["panelValues.$index.4_2"] = [
                    'required',
                    'numeric',
                    'min:50',
                    function ($attribute, $value, $fail) use ($index) {

                        $totaal = $this->fillTotaleLengte[$index] ?? 0;
                        $ruimte1 = $this->panelValues[$index]['4_1'] ?? 0;
                        $ruimte2 = $value;

                        if (!$totaal) {
                            $fail(__('messages.Vul eerst de totale paneellengte in voor dit paneel'));
                            return;
                        }

                        if ($ruimte1 < 300) {
                            $fail(__('messages.Ruimte bovenkant tot vrije ruimte moet minimaal 300mm zijn'));
                            return;
                        }

                        $marge = 300;
                        $maxRuimte2 = $totaal - $ruimte1 - $marge;

                        if ($maxRuimte2 < 0) {
                            $fail(__('messages.panelToShort'));
                            return;
                        }

                        if ($ruimte2 > $maxRuimte2) {
                            $fail(
                                __("messages.Vrije ruimte mag maximaal ") .
                                $maxRuimte2 .
                                __("mm zijn op basis van totale lengte en ruimte bovenkant tot vrije ruimte")
                            );
                        }
                    }
                ];
            }
        }
        return $rules;
    }



    public function messages(): array
    {
        return [
            'klant_naam.required' => __('messages.De klantnaam is een verplicht veld'),
            'project_naam.required' => __('messages.De projectnaam is een verplicht veld'),
            'referentie.required' => __('messages.De referentie is een verplicht veld'),
            'aflever_straat.required' => __('messages.De straat is een verplicht veld'),
            'aflever_postcode.required' => __('messages.De postcode is een verplicht veld'),
            'aflever_plaats.required' => __('messages.De plaats is een verplicht veld'),
            'aflever_land.required' => __('messages.Het land is een verplicht veld'),
            'discount.required' => __('messages.Vul aub de korting in. Als u de klant geen korting geeft, vul dan 0 in'),
            'discount.min' => __('messages.De korting kan niet lager dan 0 procent zijn'),
            'intaker.required' => __('messages.Vul aub uw naam in'),
            'fillTotaleLengte.*.min' => __('messages.De lengte moet mimimaal 500mm zijn'),
            'fillTotaleLengte.*.max' => __('messages.De lengte mag maximaal 17000mm zijn'),
            'fillTotaleLengte.*.required' => __('messages.De lengte is een verplicht veld'),
            'aantal.*.min' => __('messages.Dit moet mimimaal 1 paneel zijn'),
            'aantal.*.required' => __('messages.Het aantal panelen is een verplicht veld'),
            'cb.*.max' => __('messages.De CB mag maximaal 200mm zijn'),
            'cb.*.min' => __('messages.De CB moet minimaal 20mm zijn'),
            'lb.*.min' => __('messages.De LB moet minimaal 20mm zijn'),
            'lb.*.max' => __('messages.De LB mag maximaal 210mm zijn'),
            'panelValues.*.1.numeric' => 'Dit moet een getal zijn, hoger dan 0',
            'panelValues.*.2.numeric' => 'Dit moet een getal zijn, hoger dan 0',
            'panelValues.*.3.numeric' => 'Dit moet een getal zijn, hoger dan 0',
            'panelValues.*.3.min' =>  __('messages.De nokafschuining moet minimaal 0 graden zijn'),
            'panelValues.*.3.max' =>  __('messages.De nokafschuining mag maximaal 60 graden zijn'),
            'panelValues.*.4_1.min' =>  __('messages.Dit moet een getal hoger dan 300 mm zijn'),
            'panelValues.*.4_2.min' =>  __('messages.Dit moet een getal hoger dan 50 mm zijn'),

            'kerndikte' => __('messages.De kerndikte is een verplicht veld'),

            'requested_delivery_date.required' => __('messages.Dit is een verplicht veld'),
            'panelValues.*.1.required' => __('messages.Vul een waarde in voor Layback'),
            'panelValues.*.2.required' => __('messages.Vul een waarde in voor Nok afschuining'),
            'panelValues.*.3_1.required' => __('messages.Vul een waarde in voor Vrije ruimte 0-x1'),
            'panelValues.*.3_2.required' => __('messages.Vul een waarde in voor Vrije ruimte x1-x2'),
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
            'offerte_id' => $offerteId,
            'requested_delivery_date' => $this->requested_delivery_date,
            'comment'=> $this->comment,
            'lang'=> $this->locale,
        ]);

        $offerte = Offerte::orderBy('id', 'desc')->first();

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
                    'user_id' => Auth::user()->id,
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



        Mail::to(Auth::user()->email)->send(new sendOfferte($offerte));

        session()->flash('success',__('messages.De offerte is aangemaakt'));

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
