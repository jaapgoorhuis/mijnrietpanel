<?php

namespace App\Livewire\Offertes;

use App\Livewire\Concerns\HasPanelOptionValidation;
use App\Mail\newOrderCustomer;
use App\Mail\sendOfferte;
use App\Mail\sendOrder;
use App\Models\Application;
use App\Models\Company;
use App\Models\Offerte;
use App\Models\OfferteLines;
use App\Models\OfferteLineWaterstop;
use App\Models\Order;
use App\Models\OrderLines;
use App\Models\OrderLineWaterstop;
use App\Models\OrderTemplate;
use App\Models\PanelBrand;
use App\Models\PanelLook;
use App\Models\PanelType;
use App\Models\PriceRules;
use App\Models\Supliers;
use App\Rules\ZeroOrMinFifty;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\On;
use Livewire\Component;
use Barryvdh\DomPDF\Facade\Pdf;

class CreateOffertes extends Component
{

    use HasPanelOptionValidation;


    public $klant_naam;
    public $referentie;
    public $aflever_straat;
    public $aflever_postcode;
    public $aflever_plaats;
    public $aflever_land;

    public int $expectedRenders = 0;
    public int $receivedRenders = 0;
    public int $rendersReceived = 0;
    public $intaker;
    public $offerteId;
    public $offerte;

    public $saved = false;

    public array $selectedPanelOption = [];
    public array $panelValues = [];
    public array $fillTotaleLengte = [];
    public array $totaleLengte = [];
    public array $waterstopEnabled = [];

    public $rietkleur = 'Old look';
    public $toepassing = 'Dak';
    public $merk_paneel;
    public $aantal = [];
    public $kerndikte = '';

    public $project_naam;

    public $m2 = [];


    public $fillCb = ['0'];
    public $fillLb = ['0'];

    public $lb = [];
    public $cb = [];

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
    public bool $isSaving = false;
    public $priceRulePrice;

    public $requested_delivery_date;

    public $comment;

    public $vrijeruimtePrice;
    public $laybackPrice;
    public $nokafschuiningPrice;

    public $panelImages = [];

    public $waterstopPrice;


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
        $this->nokafschuiningPrice = \App\Models\Surcharges::where('rule', 'Nokafschuining')->first()->price;
        $this->laybackPrice = \App\Models\Surcharges::where('rule', 'Layback')->first()->price;
        $this->vrijeruimtePrice = \App\Models\Surcharges::where('rule', 'Vrije ruimte')->first()->price;
        $this->waterstopPrice = \App\Models\Surcharges::where('rule', 'Waterstop')->first()?->price ?? 0;
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
        $this->waterstopEnabled[] = false;
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
            '4_2' => 0,
            'waterstops' => [],
        ];
        $this->panelImages[] = '/storage/images/rietpanel/paneel.png';
        $this->selectedPanelOption[] = [];

    }
    public function removeOfferteLine($index)
    {
        foreach ([
                     'waterstopEnabled',
                     'offerteLines',
                     'totaleLengte',
                     'aantal',
                     'lb',
                     'cb',
                     'panelValues',
                     'selectedPanelOption',
                     'panelImages',
                     'fillTotaleLengte',
                     'fillCb',
                     'fillLb',
                     'm2',
                 ] as $property) {
            unset($this->{$property}[$index]);
            $this->{$property} = array_values($this->{$property});
        }
    }

    public function toggleWaterstop($index)
    {
        $this->waterstopEnabled[$index] = !($this->waterstopEnabled[$index] ?? false);

        if (! isset($this->panelValues[$index]['waterstops'])) {
            $this->panelValues[$index]['waterstops'] = [];
        }

        if ($this->waterstopEnabled[$index] && count($this->panelValues[$index]['waterstops']) === 0) {
            $this->addWaterstop($index);
        }

        if (! $this->waterstopEnabled[$index]) {
            $this->panelValues[$index]['waterstops'] = [];
        }

        $this->normalizePanelOptions($index);
    }

    public function addWaterstop($elementIndex)
    {
        if (! isset($this->panelValues[$elementIndex]['waterstops'])) {
            $this->panelValues[$elementIndex]['waterstops'] = [];
        }

        $this->panelValues[$elementIndex]['waterstops'][] = [
            'type' => '',
            'vertical' => '',
            'horizontal' => 0,
        ];

        $this->waterstopEnabled[$elementIndex] = true;

        $this->normalizePanelOptions($elementIndex);
    }

    public function removeWaterstop($elementIndex, $waterstopIndex)
    {
        unset($this->panelValues[$elementIndex]['waterstops'][$waterstopIndex]);

        $this->panelValues[$elementIndex]['waterstops'] = array_values(
            $this->panelValues[$elementIndex]['waterstops'] ?? []
        );

        if (count($this->panelValues[$elementIndex]['waterstops']) === 0) {
            $this->waterstopEnabled[$elementIndex] = false;
        }

        $this->normalizePanelOptions($elementIndex);
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
            'totaleLengte.*' => 'required|numeric|min:500|max:17000',
            'aantal.*' => 'required|numeric|min:1',
            'kerndikte' => 'required',
            'requested_delivery_date' => 'required',

        ];


        // Conditioneel extra rule toevoegen op lb.*

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
                            $fail(__('messages.Vul eerst de totale element lengte in voor dit element'));
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

        foreach ($this->offerteLines as $index => $line) {
            if (! ($this->waterstopEnabled[$index] ?? false)) {
                continue;
            }

            $waterstops = $this->panelValues[$index]['waterstops'] ?? [];

            foreach ($waterstops as $wsIndex => $waterstop) {
                $rules["panelValues.$index.waterstops.$wsIndex.type"] = 'required|in:960,840,730,500,300';

                $rules["panelValues.$index.waterstops.$wsIndex.vertical"] = [
                    'required',
                    'integer',
                    'min:300',
                    function ($attribute, $value, $fail) use ($index) {
                        $totaal = (int) ($this->fillTotaleLengte[$index] ?? 0);

                        if (! $totaal) {
                            $fail(__('messages.Vul eerst de totale element lengte in voor dit element'));
                            return;
                        }

                        $maxVertical = $totaal - 600;

                        if ($maxVertical < 300) {
                            $fail(__('messages.panelToShort'));
                            return;
                        }

                        if ((int) $value > $maxVertical) {
                            $fail(__('messages.De verticale positie mag maximaal ') . $maxVertical . ' mm zijn');
                        }
                    },
                ];

                $rules["panelValues.$index.waterstops.$wsIndex.horizontal"] = [
                    'required',
                    'integer',
                    function ($attribute, $value, $fail) use ($index, $wsIndex) {
                        $type = (int) ($this->panelValues[$index]['waterstops'][$wsIndex]['type'] ?? 0);
                        $max = $this->waterstopHorizontalMax($type);

                        if (! in_array($type, [960, 840, 730, 500, 300], true)) {
                            $fail(__('messages.Selecteer eerst een type waterstop'));
                            return;
                        }

                        if ((int) $value < -$max || (int) $value > $max) {
                            $fail(__('messages.De horizontale verplaatsing mag maximaal ') . $max . ' mm naar links of rechts zijn');
                        }
                    },
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
            'totaleLengte.*.min' => __('messages.De lengte moet mimimaal 500mm zijn'),
            'totaleLengte.*.max' => __('messages.De lengte mag maximaal 17000mm zijn'),
            'totaleLengte.*.required' => __('messages.De lengte is een verplicht veld'),
            'aantal.*.min' => __('messages.Dit moet mimimaal 1 element zijn'),
            'aantal.*.required' => __('messages.Het aantal elementen is een verplicht veld'),
            'cb.*.max' => __('messages.De CB mag maximaal 140mm zijn'),
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
            'panelValues.*.waterstops.*.type.required' => __('messages.Selecteer een type waterstop'),
            'panelValues.*.waterstops.*.type.in' => __('messages.Selecteer een geldig type waterstop'),
            'panelValues.*.waterstops.*.vertical.required' => __('messages.Vul de verticale positie van de waterstop in'),
            'panelValues.*.waterstops.*.vertical.integer' => __('messages.Dit moet een getal zijn'),
            'panelValues.*.waterstops.*.vertical.min' => __('messages.De verticale positie moet minimaal 300 mm zijn'),
            'panelValues.*.waterstops.*.horizontal.required' => __('messages.Vul de horizontale verplaatsing van de waterstop in'),
            'panelValues.*.waterstops.*.horizontal.integer' => __('messages.Dit moet een getal zijn'),

        ];
    }

    public function saveOfferte()
    {
        // Eerst normaliseren
        $this->normalizePanelOptions();

        if (! $this->validatePanelOptions()) {
            $this->dispatch(
                'show-form-error',
                message: __('messages.form_has_errors')
            );

            return;
        }

        // Daarna Laravel validatie
        try {
            $this->validate();
        } catch (\Illuminate\Validation\ValidationException $e) {

            $this->dispatch(
                'show-form-error',
                message: __('messages.form_has_errors')
            );

            throw $e;
        }

        $this->isSaving = true;
        $offerte = null;
        $offerteId = null;
        $waterstopsByLineIndex = [];

        DB::transaction(function () use (&$offerte, &$offerteId, &$waterstopsByLineIndex) {
            $latestOfferte = Offerte::orderBy('id', 'desc')->first();
            $currentYear = date('y');

            if ($latestOfferte && str_starts_with((string) $latestOfferte->offerte_id, $currentYear)) {
                $offerteId = (int) $latestOfferte->offerte_id + 1;
            } else {
                $offerteId = $currentYear . '0600';
            }

            $offerte = Offerte::create([
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
                'user_id' => Auth::id(),
                'status' => 'In behandeling',
                'offerte_id' => $offerteId,
                'marge' => $this->marge,
                'requested_delivery_date' => $this->requested_delivery_date,
                'comment' => $this->comment,
                'lang' => $this->locale,
            ]);


            foreach ($this->offerteLines as $index => $key) {
                $selectedOptions = $this->selectedPanelOption[$index] ?? [];

                $waterstops = ($this->waterstopEnabled[$index] ?? false)
                    ? array_values($this->panelValues[$index]['waterstops'] ?? [])
                    : [];

                $firstWaterstop = $waterstops[0] ?? null;

                $waterstopsByLineIndex[$index] = $waterstops;

                OfferteLines::create([
                    'offerte_id' => $offerte->id,
                    'fillLb' => $this->fillLb[$index] ?? 0,
                    'fillTotaleLengte' => $this->fillTotaleLengte[$index] ?? 0,
                    'aantal' => $this->aantal[$index] ?? 0,
                    'user_id' => Auth::id(),
                    'm2' => $this->m2[$index] ?? 0,

                    'lb' => in_array(1, $selectedOptions) ? ($this->panelValues[$index][1] ?? 0) : 0,
                    'nokafschuining' => in_array(3, $selectedOptions) ? ($this->panelValues[$index][3] ?? 0) : 0,
                    'vrije_ruimte_1' => in_array(4, $selectedOptions) ? ($this->panelValues[$index]['4_1'] ?? 0) : 0,
                    'vrije_ruimte_2' => in_array(4, $selectedOptions) ? ($this->panelValues[$index]['4_2'] ?? 0) : 0,
                    'fillCb' => in_array(2, $selectedOptions) ? ($this->panelValues[$index][2] ?? 0) : 0,

                    'waterstop_type' => $firstWaterstop ? ($firstWaterstop['type'] ?? null) : null,
                    'waterstop_vertical' => $firstWaterstop ? ($firstWaterstop['vertical'] ?? null) : null,
                    'waterstop_horizontal' => $firstWaterstop ? ($firstWaterstop['horizontal'] ?? 0) : null,
                ]);
            }


            foreach ($offerte->offerteLines->values() as $index => $offerteLine) {
                foreach (($waterstopsByLineIndex[$index] ?? []) as $waterstop) {
                    OfferteLineWaterstop::create([
                        'offerte_line_id' => $offerteLine->id,
                        'type' => (int) $waterstop['type'],
                        'vertical' => (int) $waterstop['vertical'],
                        'horizontal' => (int) ($waterstop['horizontal'] ?? 0),
                    ]);
                }
            }
        });


        $offerte->refresh();
        $offerte->load(['offerteLines', 'user', 'surcharges']);
        app(\App\Services\PricingServices::class)->updateDocumentPricing($offerte);
        $offerte->refresh();
        $offerte->load(['OfferteLines', 'user', 'surcharges']);

        $this->receivedRenders = 0;
        $this->expectedRenders = count($this->offerteLines);
        $this->offerteId = $offerte->id;

        $this->dispatch('capture-panel-renders');


    }

    #[On('save-panel-render')]
    public function savePanelRender($index, $image)
    {
        $offerteLine = OfferteLines::where('offerte_id', $this->offerteId)
            ->orderBy('id')
            ->get()
            ->values()
            ->get($index);

        if (! $offerteLine) {
            return;
        }

        $image = str_replace('data:image/png;base64,', '', $image);
        $image = str_replace(' ', '+', $image);

        $filename = 'offerteline-'.$offerteLine->id.'.png';

        \Storage::disk('public')->put(
            'offertelines/renders/'.$filename,
            base64_decode($image)
        );

        $offerteLine->update([
            'render_image' => 'offertelines/renders/'.$filename,
        ]);

        $this->receivedRenders++;

        if ($this->receivedRenders >= $this->expectedRenders) {
            $this->finishOfferte();
        }
    }

    public function finishOfferte()
    {

        $offerte = Offerte::with([
            'offertelines.waterstops',
            'user',
            'surcharges',

        ])->findOrFail($this->offerteId);


        Pdf::loadView('pdf.offerte', [
            'offerte' => $offerte,
            'offerteLines' => $offerte->offerteLines,
            'showNokafschuining' => $offerte->offerteLines->where('nokafschuining','>',0)->count() > 0,
            'showLb' => $offerte->offerteLines->where('lb','>',0)->count() > 0,
            'showCb' => $offerte->offerteLines->where('fillCb','>',0)->count() > 0,
            'showWaterstop' => $offerte->offerteLines->contains(fn($line)=>$line->waterstops->count()>0),
            'showVrijeRuimte' => $offerte->offerteLines->where('vrije_ruimte_2','>',0)->count()>0,
        ])
            ->save(public_path('/storage/offertes/offerte-'.$offerte->offerte_id.'.pdf'));


        Mail::to(Auth::user()->email)->send(new sendOfferte($offerte));


        session()->flash('success', __('messages.De offerte is aangemaakt'));


        return $this->redirect('/offertes', navigate:true);
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
