<?php

namespace App\Livewire\Orders;

use AllowDynamicProperties;
use App\Mail\newOrderCustomer;
use App\Mail\sendOrder;
use App\Models\Company;
use App\Models\Order;
use App\Models\OrderLines;
use App\Models\OrderLineWaterstop;
use App\Models\PanelType;
use App\Models\PriceRules;
use App\Models\Supliers;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\On;
use Livewire\Component;
use App\Livewire\Concerns\HasPanelOptionValidation;

class CreateOrders extends Component
{
    use HasPanelOptionValidation;

    public $intaker;
    public int $expectedRenders = 0;
    public int $receivedRenders = 0;
    public bool $isSaving = false;
    public int $rendersReceived = 0;
    public $klant_naam;
    public $referentie;
    public $aflever_straat;
    public $aflever_postcode;
    public $aflever_plaats;
    public $aflever_land;
    public $orderId;
    public $rietkleur = 'Old look';
    public $toepassing = 'Dak';
    public $merk_paneel;
    public $kerndikte = '';

    public $aantal = [];
    public $m2 = [];
    public $discount = 0;

    public $fillTotaleLengte = [];
    public $fillCb = [];
    public $fillLb = [];

    public $lb = [];
    public $cb = [];
    public $totaleLengte = [];

    public $panelTypes;
    public $brands = [];

    public $orderLines = [];

    public $saved = false;

    public $wandSupliers;
    public $dakSupliers;
    public $supliers;

    public $project_naam;

    public $priceRule;
    public $company;
    public $companyDiscount;
    public $werkendeBreedte;
    public $priceRulePrice = 0;

    public $requested_delivery_date;

    public $marge = 0;
    public array $panelRenders = [];
    public $locale;
    public $comment;

    public $selectedPanelOption = [];
    public $panelValues = [];

    public $vrijeruimtePrice;
    public $laybackPrice;
    public $waterstopPrice;
    public $nokafschuiningPrice;

    public $panelImages = [];

    public array $waterstopEnabled = [];

    public function mount()
    {
        if (Auth::user()->bedrijf_id == 0) {
            session()->flash('error', 'Uw account is niet gekoppeld aan een bedrijf. Hierdoor kunt u geen orders plaatsen. Neem contact met rietpanel op om dit probleem te verhelpen.');
            return $this->redirect('/orders', navigate: true);
        }

        if (! Auth::user()->is_admin && Auth::user()->is_architect) {
            session()->flash('error', 'U heeft geen rechten voor deze pagina');
            return $this->redirect('/dashboard', navigate: true);
        }

        $this->wandSupliers = Supliers::where('toepassing_wand', 1)->get();
        $this->dakSupliers = Supliers::where('toepassing_dak', 1)->get();
        $this->supliers = Supliers::get();

        $this->brands = $this->dakSupliers;

        if ($this->dakSupliers->first()) {
            $this->merk_paneel = $this->dakSupliers->first()->name;
            $this->werkendeBreedte = $this->dakSupliers->first()->werkende_breedte;
        }

        $this->locale = config('app.locale');

        $this->company = Company::where('id', Auth::user()->bedrijf_id)->first();
        $this->companyDiscount = $this->company?->discount ?? 0;

        $this->priceRule = PriceRules::where('company_id', '0')
            ->where('reseller', 0)
            ->where('panel_type', '1')
            ->first();

        $this->panelTypes = PanelType::whereIn('id', PriceRules::pluck('panel_type'))->get();

        $this->priceRulePrice = 0;
    }

    public function render()
    {
        $this->nokafschuiningPrice = \App\Models\Surcharges::where('rule', 'Nokafschuining')->first()?->price ?? 0;
        $this->laybackPrice = \App\Models\Surcharges::where('rule', 'Layback')->first()?->price ?? 0;
        $this->waterstopPrice = \App\Models\Surcharges::where('rule', 'Waterstop')->first()?->price ?? 0;
        $this->vrijeruimtePrice = \App\Models\Surcharges::where('rule', 'Vrije ruimte')->first()?->price ?? 0;

        return view('livewire.orders.createOrder');
    }

    public function updateSelectedPanelOption($index)
    {

        if (! $this->hasValidPanelLength((int) $index)) {
            $this->selectedPanelOption[$index] = [];
            $this->panelImages[$index] = '/storage/images/rietpanel/paneel.png';
            return;
        }

        $this->normalizePanelOptions((int) $index);
        $this->normalizePanelOptions($index);

        $options = $this->selectedPanelOption[$index] ?? [];

        if (empty($options)) {
            $this->panelImages[$index] = '/storage/images/rietpanel/paneel.png';
            return;
        }

        sort($options);

        $this->panelImages[$index] = "/storage/images/rietpanel/paneel-" . implode('-', $options) . ".png";
    }

    public function updatePanelValues($index, $key)
    {
        if (! isset($this->panelValues[$index])) {
            $this->panelValues[$index] = [];
            if (isset($index)) {
                $this->normalizePanelOptions((int) $index);
            } elseif (isset($key)) {
                $this->normalizePanelOptions((int) $key);
            }
        }

        $this->normalizePanelOptions($index);
    }

    public function updatePrice()
    {
        if ($this->kerndikte != '') {
            $panelType = PanelType::where('name', $this->kerndikte)->first();

            if ($panelType && $panelType->priceRule) {
                $this->priceRule = $panelType->priceRule;
                $this->priceRulePrice = $this->priceRule->price;
            } else {
                $this->priceRulePrice = 0;
            }
        } else {
            $this->priceRulePrice = 0;
        }
    }

    public function updateCb($index)
    {
        $this->cb[$index] = $this->fillCb[$index] ?: '20';
    }

    public function updateLb($index)
    {
        $this->lb[$index] = $this->fillLb[$index] ?: '0';
    }

    public function updateTotaleLengte($index)
    {
        $this->totaleLengte[$index] = $this->fillTotaleLengte[$index] ?: '';
        $this->normalizePanelOptions((int) $index);
        $this->updateM2($index);
    }

    public function updateBrands()
    {
        if ($this->toepassing == 'Wand') {
            $this->brands = $this->wandSupliers;
        } elseif ($this->toepassing == 'Dak') {
            $this->merk_paneel = $this->dakSupliers->first()?->name;
            $this->brands = $this->dakSupliers;
        }
    }

    public function addOrderLine()
    {
        $this->waterstopEnabled[] = false;
        $this->orderLines[] = '';
        $this->fillCb[] = '20';
        $this->cb[] = '20';
        $this->m2[] = '0';
        $this->lb[] = '0';
        $this->fillLb[] = '0';
        $this->totaleLengte[] = '';
        $this->fillTotaleLengte[] = '';
        $this->aantal[] = '';
        $this->panelValues[] = [
            1 => 20,
            2 => 20,
            3 => 0,
            '4_1' => 0,
            '4_2' => 0,
            'waterstops' => [],
        ];
        $this->panelImages[] = '/storage/images/rietpanel/paneel.png';
        $this->selectedPanelOption[] = [];
    }

    public function removeOrderLine($index)
    {
        foreach ([
                     'waterstopEnabled',
                     'orderLines',
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
            'discount' => 'required|min:0',
            'fillTotaleLengte.*' => 'required|numeric|min:500|max:14500',
            'aantal.*' => 'required|numeric|min:1',
            'kerndikte' => 'required',
            'requested_delivery_date' => 'required',
        ];

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
                $rules["panelValues.$index.4_1"] = 'required|numeric|min:300';

                $rules["panelValues.$index.4_2"] = [
                    'required',
                    'numeric',
                    'min:50',
                    function ($attribute, $value, $fail) use ($index) {
                        $totaal = $this->fillTotaleLengte[$index] ?? 0;
                        $ruimte1 = $this->panelValues[$index]['4_1'] ?? 0;
                        $ruimte2 = $value;

                        if (! $totaal) {
                            $fail(__('messages.Vul eerst de totale element lengte in voor dit element'));
                            return;
                        }

                        if ($ruimte1 < 300) {
                            $fail(__('messages.Ruimte bovenkant tot vrije ruimte moet minimaal 300mm zijn'));
                            return;
                        }

                        $maxRuimte2 = $totaal - $ruimte1 - 300;

                        if ($maxRuimte2 < 0) {
                            $fail(__('messages.panelToShort'));
                            return;
                        }

                        if ($ruimte2 > $maxRuimte2) {
                            $fail(
                                __('messages.Vrije ruimte mag maximaal ') .
                                $maxRuimte2 .
                                __('messages.mm zijn op basis van totale lengte en ruimte bovenkant tot vrije ruimte')
                            );
                        }
                    },
                ];
            }
        }

        foreach ($this->orderLines as $index => $line) {
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
            'fillTotaleLengte.*.min' => __('messages.De lengte moet mimimaal 500mm zijn'),
            'fillTotaleLengte.*.max' => __('messages.De lengte mag maximaal 14500mm zijn'),
            'fillTotaleLengte.*.required' => __('messages.De lengte is een verplicht veld'),
            'aantal.*.min' => __('messages.Dit moet mimimaal 1 element zijn'),
            'aantal.*.required' => __('messages.Het aantal elementen is een verplicht veld'),
            'panelValues.*.1.required' => __('messages.Vul een waarde in voor Layback'),
            'panelValues.*.1.numeric' => __('messages.Dit moet een getal zijn'),
            'panelValues.*.2.required' => __('messages.Vul een waarde in voor Cutback'),
            'panelValues.*.2.numeric' => __('messages.Dit moet een getal zijn'),
            'panelValues.*.3.required' => __('messages.Vul een waarde in voor Nok afschuining'),
            'panelValues.*.3.numeric' => __('messages.Dit moet een getal zijn'),
            'panelValues.*.3.min' => __('messages.De nokafschuining moet minimaal 1 graad zijn'),
            'panelValues.*.3.max' => __('messages.De nokafschuining mag maximaal 60 graden zijn'),
            'panelValues.*.4_1.min' => __('messages.Dit moet een getal hoger dan 300 mm zijn'),
            'panelValues.*.4_2.min' => __('messages.Dit moet een getal hoger dan 50 mm zijn'),
            'panelValues.*.waterstops.*.type.required' => __('messages.Selecteer een type waterstop'),
            'panelValues.*.waterstops.*.type.in' => __('messages.Selecteer een geldig type waterstop'),
            'panelValues.*.waterstops.*.vertical.required' => __('messages.Vul de verticale positie van de waterstop in'),
            'panelValues.*.waterstops.*.vertical.integer' => __('messages.Dit moet een getal zijn'),
            'panelValues.*.waterstops.*.vertical.min' => __('messages.De verticale positie moet minimaal 300 mm zijn'),
            'panelValues.*.waterstops.*.horizontal.required' => __('messages.Vul de horizontale verplaatsing van de waterstop in'),
            'panelValues.*.waterstops.*.horizontal.integer' => __('messages.Dit moet een getal zijn'),
            'kerndikte.required' => __('messages.De kerndikte is een verplicht veld'),
            'requested_delivery_date.required' => __('messages.Dit is een verplicht veld'),
        ];
    }

    public function saveOrder()
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
        $order = null;
        $orderId = null;
        $waterstopsByLineIndex = [];

        DB::transaction(function () use (&$order, &$orderId, &$waterstopsByLineIndex) {
            $latestOrder = Order::orderBy('id', 'desc')->first();
            $currentYear = date('y');

            if ($latestOrder && str_starts_with((string) $latestOrder->order_id, $currentYear)) {
                $orderId = (int) $latestOrder->order_id + 1;
            } else {
                $orderId = $currentYear . '0600';
            }

            $order = Order::create([
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
                'order_id' => $orderId,
                'marge' => $this->marge,
                'requested_delivery_date' => $this->requested_delivery_date,
                'comment' => $this->comment,
                'lang' => $this->locale,
            ]);


            foreach ($this->orderLines as $index => $key) {
                $selectedOptions = $this->selectedPanelOption[$index] ?? [];

                $waterstops = ($this->waterstopEnabled[$index] ?? false)
                    ? array_values($this->panelValues[$index]['waterstops'] ?? [])
                    : [];

                $firstWaterstop = $waterstops[0] ?? null;

                $waterstopsByLineIndex[$index] = $waterstops;

                OrderLines::create([
                    'order_id' => $order->id,
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


            foreach ($order->orderLines->values() as $index => $orderLine) {
                foreach (($waterstopsByLineIndex[$index] ?? []) as $waterstop) {
                    OrderLineWaterstop::create([
                        'order_line_id' => $orderLine->id,
                        'type' => (int) $waterstop['type'],
                        'vertical' => (int) $waterstop['vertical'],
                        'horizontal' => (int) ($waterstop['horizontal'] ?? 0),
                    ]);
                }
            }
        });


        $order->refresh();
        $order->load(['orderLines', 'user', 'surcharges', 'orderRules']);
        app(\App\Services\PricingServices::class)->updateDocumentPricing($order);
        $order->refresh();
        $order->load(['orderLines', 'user', 'surcharges', 'orderRules']);

        $this->receivedRenders = 0;
        $this->expectedRenders = count($this->orderLines);
        $this->orderId = $order->id;

        $this->dispatch('capture-panel-renders');


    }

    #[On('save-panel-render')]
    #[On('save-panel-render')]
    public function savePanelRender($index, $image)
    {
        $orderLine = OrderLines::where('order_id', $this->orderId)
            ->orderBy('id')
            ->get()
            ->values()
            ->get($index);

        if (! $orderLine) {
            return;
        }

        $image = str_replace('data:image/png;base64,', '', $image);
        $image = str_replace(' ', '+', $image);

        $filename = 'orderline-'.$orderLine->id.'.png';

        \Storage::disk('public')->put(
            'orderlines/renders/'.$filename,
            base64_decode($image)
        );

        $orderLine->update([
            'render_image' => 'orderlines/renders/'.$filename,
        ]);

        $this->receivedRenders++;

        if ($this->receivedRenders >= $this->expectedRenders) {
            $this->finishOrder();
        }
    }

    public function finishOrder()
    {

        $order = Order::with([
            'orderLines.waterstops',
            'user',
            'surcharges',
            'orderRules'
        ])->findOrFail($this->orderId);


        Pdf::loadView('pdf.order', [
            'order' => $order,
            'orderLines' => $order->orderLines,
            'showNokafschuining' => $order->orderLines->where('nokafschuining','>',0)->count() > 0,
            'showLb' => $order->orderLines->where('lb','>',0)->count() > 0,
            'showCb' => $order->orderLines->where('fillCb','>',0)->count() > 0,
            'showWaterstop' => $order->orderLines->contains(fn($line)=>$line->waterstops->count()>0),
            'showVrijeRuimte' => $order->orderLines->where('vrije_ruimte_2','>',0)->count()>0,
        ])
            ->save(public_path('/storage/orders/order-'.$order->order_id.'.pdf'));


        Mail::to(env('MAIL_TO_ADDRESS'))
            ->send(new sendOrder($order));


        Mail::to(Auth::user()->email)
            ->send(new newOrderCustomer($order));


        session()->flash(
            'success',
            __('messages.De order is aangemaakt. Wij controleren de order en zullen deze zo spoedig mogelijk bevestigen')
        );


        return $this->redirect('/orders', navigate:true);
    }

//    public function updatedFillTotaleLengte($value, $index): void
//    {
//        $this->totaleLengte[$index] = $value ?: '';
//
//        $this->updateM2($index);
//
//        if (is_numeric($value) && (int) $value >= 500 && (int) $value <= 14500) {
//            $this->resetErrorBag("totaleLengte.$index");
//            $this->resetErrorBag("fillTotaleLengte.$index");
//        }
//    }

    private function waterstopsHaveOverlap(): bool
    {

        foreach ($this->orderLines as $index => $line) {
            if (! ($this->waterstopEnabled[$index] ?? false)) {
                continue;
            }

            $waterstops = $this->panelValues[$index]['waterstops'] ?? [];

            foreach ($waterstops as $aIndex => $a) {
                foreach ($waterstops as $bIndex => $b) {
                    if ($aIndex >= $bIndex) {
                        continue;
                    }

                    if ($this->waterstopsOverlap($a, $b)) {
                        $this->addError(
                            "panelValues.$index.waterstops.$aIndex.vertical",
                            __('messages.Waterstops mogen elkaar niet overlappen')
                        );

                        $this->addError(
                            "panelValues.$index.waterstops.$bIndex.vertical",
                            __('messages.Waterstops mogen elkaar niet overlappen')
                        );

                        return true;
                    }
                }
            }
        }

        return false;
    }

    private function waterstopsOverlap(array $a, array $b): bool
    {
        $height = 200;

        $aTop = (int) ($a['vertical'] ?? 0);
        $aBottom = $aTop + $height;

        $bTop = (int) ($b['vertical'] ?? 0);
        $bBottom = $bTop + $height;

        if ($aBottom <= $bTop || $bBottom <= $aTop) {
            return false;
        }

        $aWidth = (int) ($a['type'] ?? 0);
        $bWidth = (int) ($b['type'] ?? 0);

        $aCenter = (int) ($a['horizontal'] ?? 0);
        $bCenter = (int) ($b['horizontal'] ?? 0);

        $aLeft = $aCenter - ($aWidth / 2);
        $aRight = $aCenter + ($aWidth / 2);

        $bLeft = $bCenter - ($bWidth / 2);
        $bRight = $bCenter + ($bWidth / 2);

        return $aLeft < $bRight && $aRight > $bLeft;
    }

    public function cancelCreateOrder()
    {
        return $this->redirect('/orders', navigate: true);
    }

    public function updateM2($index)
    {
        foreach ($this->brands as $brand) {
            if ($brand->name == $this->merk_paneel) {
                $this->werkendeBreedte = $brand->werkende_breedte;
            }
        }

        $lengtePaneel = (int) ($this->fillTotaleLengte[$index] ?? 0);
        $werkendeBreedteM = $this->werkendeBreedte / 1000;
        $lengtePaneelM = $lengtePaneel / 1000;

        if ($lengtePaneel == 0 || $this->werkendeBreedte == 0) {
            $this->m2[$index] = 0;
        } else {
            $this->m2[$index] = round($lengtePaneelM * $werkendeBreedteM * intval($this->aantal[$index] ?? 0), 2);
        }
    }
}
