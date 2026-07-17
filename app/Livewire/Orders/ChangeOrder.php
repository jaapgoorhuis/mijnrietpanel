<?php

namespace App\Livewire\Orders;

use App\Livewire\Concerns\HasPanelOptionValidation;
use App\Mail\orderUpdated;
use App\Models\Company;
use App\Models\Order;
use App\Models\OrderLines;
use App\Models\OrderLineWaterstop;
use App\Models\OrderRules;
use App\Models\PanelType;
use App\Models\PriceRules;
use App\Models\Supliers;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class ChangeOrder extends Component
{
    use HasPanelOptionValidation;

    public $klant_naam, $referentie, $aflever_straat, $aflever_postcode, $aflever_plaats, $aflever_land;
    public $intaker, $rietkleur = 'Old look', $toepassing = 'Dak', $merk_paneel, $kerndikte, $project_naam;
    public $aantal = [], $m2 = [], $fillTotaleLengte = ['0'], $fillCb = ['0'], $fillLb = ['0'];
    public $lb = [], $cb = [], $totaleLengte = [], $brands = [], $orderLines = [];
    public $panelTypes;
    public $wandSupliers, $dakSupliers, $werkendeBreedte, $company, $companyDiscount, $priceRule, $order;
    public $discount = 0;
    public $deletedOrderLines = [], $marge = 0, $order_id, $creator_user_id, $exsistingOrderLines, $creator_user;
    public $priceRulePrice, $saved = false, $requested_delivery_date, $comment;
    public $confirmedOrder = false, $showConfirmModal = false;
    public $selectedPanelOption = [], $panelValues = [], $vrijeruimtePrice, $laybackPrice, $nokafschuiningPrice, $waterstopPrice, $panelImages = [];
    public array $waterstopEnabled = [];

    public bool $showPriceUpdateModal = false;
    public string $priceUpdateMessage = '';
    public bool $recalculateOrderPrices = false;

    public function mount($id)
    {
        if (Auth::user()->bedrijf_id == 0) {
            session()->flash('error', 'Uw account is niet gekoppeld aan een bedrijf.');
            return $this->redirect('/orders', navigate: true);
        }

        $this->order_id = $id;
        $this->order = Order::with(['orderLines.waterstops', 'user', 'orderRules', 'surcharges'])->findOrFail($id);

        $latestPriceRule = \App\Models\PriceRules::latest('updated_at')->first();
        $latestSurcharge = \App\Models\Surcharges::latest('updated_at')->first();

        $latestPricingUpdate = collect([
            optional($latestPriceRule)->updated_at,
            optional($latestSurcharge)->updated_at,
        ])->filter()->max();

        if (
            auth()->user()->is_admin &&
            $latestPricingUpdate &&
            $this->order->prices_updated_at &&
            $this->order->prices_updated_at < $latestPricingUpdate
        ) {
            $this->showPriceUpdateModal = true;
            $this->priceUpdateMessage = __('messages.price_update_warning_order', [
                'date' => $latestPricingUpdate->format('d-m-Y H:i'),
            ]);
        }

        $this->wandSupliers = Supliers::where('toepassing_wand', 1)->get();
        $this->dakSupliers = Supliers::where('toepassing_dak', 1)->get();
        $this->brands = $this->dakSupliers;
        $this->panelTypes = PanelType::whereIn('id', PriceRules::pluck('panel_type'))->get();

        $this->creator_user_id = $this->order->user_id;
        $this->creator_user = User::find($this->creator_user_id);
        $this->company = Company::find($this->creator_user->bedrijf_id);
        $this->companyDiscount = $this->company->discount;

        $this->exsistingOrderLines = OrderLines::with('waterstops')->where('order_id', $id)->get();

        $this->werkendeBreedte = $this->dakSupliers->first()->werkende_breedte;

        $this->klant_naam = $this->order->klantnaam;
        $this->referentie = $this->order->referentie;
        $this->aflever_straat = $this->order->aflever_straat;
        $this->aflever_postcode = $this->order->aflever_postcode;
        $this->aflever_land = $this->order->aflever_land;
        $this->aflever_plaats = $this->order->aflever_plaats;
        $this->intaker = $this->order->intaker;
        $this->rietkleur = $this->order->rietkleur;
        $this->toepassing = $this->order->toepassing;
        $this->merk_paneel = $this->order->merk_paneel;
        $this->kerndikte = $this->order->kerndikte;
        $this->discount = $this->order->discount;
        $this->project_naam = $this->order->project_naam;
        $this->marge = $this->order->marge;
        $this->requested_delivery_date = $this->order->requested_delivery_date;
        $this->comment = $this->order->comment;

        $this->priceRule = PanelType::where('name', $this->kerndikte)->first();

        if ($this->priceRule && $this->priceRule->priceRule) {
            $this->priceRulePrice = $this->priceRule->priceRule->price;
        } else {
            $this->priceRulePrice = 0;
            $this->kerndikte = '';
        }

        foreach ($this->exsistingOrderLines as $key => $exsistingOrderLine) {
            $this->orderLines[] = $key;
            $this->fillCb[$key] = $exsistingOrderLine->fillCb;
            $this->fillTotaleLengte[$key] = $exsistingOrderLine->fillTotaleLengte;
            $this->aantal[$key] = $exsistingOrderLine->aantal;
            $this->m2[$key] = $exsistingOrderLine->m2;
            $this->cb[$key] = $exsistingOrderLine->fillCb;
            $this->totaleLengte[$key] = $exsistingOrderLine->fillTotaleLengte;

            $waterstops = $exsistingOrderLine->waterstops
                ->map(fn ($ws) => [
                    'type' => (string) $ws->type,
                    'vertical' => $ws->vertical,
                    'horizontal' => $ws->horizontal,
                ])
                ->values()
                ->toArray();

            $this->panelValues[$key] = [
                1 => $exsistingOrderLine->lb ?? 0,
                2 => $exsistingOrderLine->fillCb ?? 0,
                3 => $exsistingOrderLine->nokafschuining ?? 0,
                '4_1' => $exsistingOrderLine->vrije_ruimte_1 ?? 0,
                '4_2' => $exsistingOrderLine->vrije_ruimte_2 ?? 0,
                'waterstops' => $waterstops,
            ];

            $this->waterstopEnabled[$key] = count($waterstops) > 0;

            $this->selectedPanelOption[$key] = [];

            if ($exsistingOrderLine->lb > 0) $this->selectedPanelOption[$key][] = 1;
            if ($exsistingOrderLine->fillCb > 0) $this->selectedPanelOption[$key][] = 2;
            if ($exsistingOrderLine->nokafschuining > 0) $this->selectedPanelOption[$key][] = 3;
            if ($exsistingOrderLine->vrije_ruimte_1 > 0 || $exsistingOrderLine->vrije_ruimte_2 > 0) $this->selectedPanelOption[$key][] = 4;

            if (empty($this->selectedPanelOption[$key])) {
                $this->panelImages[$key] = '/storage/images/rietpanel/paneel.png';
            } else {
                $options = $this->selectedPanelOption[$key];
                sort($options);
                $this->panelImages[$key] = "/storage/images/rietpanel/paneel-" . implode('-', $options) . ".png";
            }

            $this->normalizePanelOptions((int) $key);
        }

        if ($this->order->status === 'Bevestigd') {
            $this->showConfirmModal = true;
            $this->confirmedOrder = true;
        }

        if (! Auth::user()->is_admin && Auth::user()->is_architect) {
            session()->flash('error', 'U heeft geen rechten voor deze pagina');
            return $this->redirect('/dashboard', navigate: true);
        }
    }

    public function render()
    {
        $this->nokafschuiningPrice = \App\Models\Surcharges::where('rule', 'Nokafschuining')->first()?->price ?? 0;
        $this->laybackPrice = \App\Models\Surcharges::where('rule', 'Layback')->first()?->price ?? 0;
        $this->vrijeruimtePrice = \App\Models\Surcharges::where('rule', 'Vrije ruimte')->first()?->price ?? 0;
        $this->waterstopPrice = \App\Models\Surcharges::where('rule', 'Waterstop')->first()?->price ?? 0;

        return view('livewire.orders.changeOrder');
    }

    public function keepOldOrderPrices()
    {
        $this->recalculateOrderPrices = false;
        $this->showPriceUpdateModal = false;
    }

    public function useNewOrderPrices()
    {
        $this->recalculateOrderPrices = true;
        $this->showPriceUpdateModal = false;
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
        $panelType = PanelType::where('name', $this->kerndikte)->first();

        if ($panelType && $panelType->priceRule) {
            $this->priceRule = $panelType->priceRule;
            $this->priceRulePrice = $this->priceRule->price;
        } else {
            $this->priceRulePrice = 0;
        }
    }

    public function updateCb($index)
    {
        $this->cb[$index] = $this->fillCb[$index] ?: '0';
    }

    public function updateLb($index)
    {
        $this->lb[$index] = $this->fillLb[$index] ?: '0';
    }

    public function updateTotaleLengte($index)
    {
        $this->totaleLengte[$index] = $this->fillTotaleLengte[$index] ?: '0';
        $this->normalizePanelOptions((int) $index);
        $this->updateM2($index);
    }

    public function updateBrands()
    {
        if ($this->toepassing === 'Wand') {
            $this->brands = $this->wandSupliers;
        } elseif ($this->toepassing === 'Dak') {
            $this->merk_paneel = $this->dakSupliers->first()->name;
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
        $this->aantal[] = '0';

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
        if (isset($this->exsistingOrderLines[$index])) {
            $this->deletedOrderLines[] = $this->exsistingOrderLines[$index]->id;
        }

        foreach ([
                     'orderLines', 'totaleLengte', 'aantal', 'lb', 'cb', 'fillCb', 'fillLb',
                     'fillTotaleLengte', 'm2', 'panelValues', 'selectedPanelOption', 'panelImages', 'waterstopEnabled'
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
            'kerndikte' => 'required',
            'fillTotaleLengte.*' => 'required|numeric|min:500|max:17000',
            'aantal.*' => 'required|numeric|min:1',
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
            'fillTotaleLengte.*.max' => __('messages.De lengte mag maximaal 17000mm zijn'),
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
        $this->fillTotaleLengte = array_values(array_filter($this->fillTotaleLengte, fn ($v) => $v !== ''));

        $this->normalizePanelOptions();

        if (! $this->validatePanelOptions()) {
            $this->dispatch('show-form-error', message: __('messages.form_has_errors'));
            return;
        }

        try {
            $this->validate();
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('show-form-error', message: __('messages.form_has_errors'));
            throw $e;
        }

        $order = Order::with(['orderLines.waterstops', 'user', 'orderRules', 'surcharges'])->findOrFail($this->order_id);

        $wasConfirmed = $order->status === 'Bevestigd';

        $oldLinePrices = $order->orderLines
            ->values()
            ->mapWithKeys(fn ($line, $index) => [$index => $line->price_per_m2]);

        DB::transaction(function () use ($order, $wasConfirmed, $oldLinePrices) {
            $order->update([
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
                'marge' => $this->marge,
                'user_id' => $this->creator_user_id,
                'status' => 'In behandeling',
                'requested_delivery_date' => $this->requested_delivery_date,
                'comment' => $this->comment,
            ]);

            if ($wasConfirmed) {
                OrderRules::where('order_id', $order->id)->delete();
            }

            // Eerst de waterstops van de bestaande orderregels opruimen, dan pas de regels zelf
            OrderLineWaterstop::whereIn('order_line_id', $order->orderLines->pluck('id'))->delete();
            OrderLines::where('order_id', $order->id)->delete();

            $waterstopsByLineIndex = [];

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

                    'price_per_m2' => $this->recalculateOrderPrices ? null : ($oldLinePrices[$index] ?? 0),
                ]);
            }

            foreach ($order->orderLines()->get()->values() as $index => $orderLine) {
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
        $order->load(['orderLines.waterstops', 'user', 'orderRules', 'surcharges']);

        if ($this->recalculateOrderPrices || $wasConfirmed) {
            app(\App\Services\PricingServices::class)->updateDocumentPricing($order);

            $order->refresh();
            $order->load(['orderLines.waterstops', 'user', 'orderRules', 'surcharges']);
        } else {
            $subtotal = $order->orderLines->sum(fn ($line) => (float) $line->m2 * (float) $line->price_per_m2);
            $surchargesTotal = (float) ($order->surcharges_total ?? 0);
            $rulePrice = $order->orderRules ? (float) $order->orderRules->sum('price') : 0;

            $vatBase = $subtotal + $surchargesTotal + $rulePrice;
            $vat = $vatBase * 0.21;

            $order->subtotal = $subtotal;
            $order->vat_total = $vat;
            $order->grand_total = $vatBase + $vat;
            $order->save();

            $order->refresh();
            $order->load(['orderLines.waterstops', 'user', 'orderRules', 'surcharges']);
        }

        $orderLines = $order->orderLines;
        $user = User::find($order->user_id);

        $showNokafschuining = $orderLines->where('nokafschuining', '>', 0)->count() > 0;
        $showVrijeRuimte = $orderLines->where('vrije_ruimte_2', '>', 0)->count() > 0;
        $showCb = $orderLines->where('fillCb', '>', 0)->count() > 0;
        $showLb = $orderLines->where('lb', '>', 0)->count() > 0;
        $showWaterstop = $orderLines->contains(fn ($line) => $line->waterstops->count() > 0);

        Pdf::loadView('pdf.order', [
            'order' => $order,
            'orderLines' => $orderLines,
            'showNokafschuining' => $showNokafschuining,
            'showLb' => $showLb,
            'showCb' => $showCb,
            'showVrijeRuimte' => $showVrijeRuimte,
            'showWaterstop' => $showWaterstop,
        ])->save(public_path('/storage/orders/order-' . $order->order_id . '.pdf'));

        if (Auth::user()->is_admin == 1 && $order->user_id != Auth::user()->id) {
            Mail::to($user->email)->send(new orderUpdated($order));
        }

        session()->flash('success', __('messages.De order is bewerkt'));

        return $this->redirect('/orders', navigate: true);
    }

    public function cancelChangeOrder()
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
