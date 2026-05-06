<?php

namespace App\Livewire\Orders;

use App\Mail\orderUpdated;
use App\Mail\sendOrder;
use App\Models\Application;
use App\Models\Company;
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

class ChangeOrder extends Component
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


    public $orderLines = [];
    public $wandSupliers;
    public $dakSupliers;

    public $werkendeBreedte;
    public $orderLineValues = [];
    public $company;
    public $companyDiscount;
    public $priceRule;
    public $order;

    public $deletedOrderLines = [];

    public $marge = 0;
    public $order_id;

    public $creator_user_id;

    public $exsistingOrderLines;
    public $creator_user;

    public $priceRulePrice;
    public $saved = FALSE;
    public $requested_delivery_date;
    public $comment;
    public $confirmedOrder = false;
    public $showConfirmModal = false;

    public $selectedPanelOption =[];
    public $panelValues =[];
    public $vrijeruimtePrice;
    public $laybackPrice;
    public $nokafschuiningPrice;
    public $panelImages = [];
    public function mount($id) {


        if(Auth::user()->bedrijf_id == 0) {
            session()->flash('error', 'Uw account is niet gekoppeld aan een bedrijf. Hierdoor kunt u geen orderss plaatsen. Neem contact met rietpanel op om dit probleem te verhelpen.');
            return $this->redirect('/orders', navigate: true);
        }
        $this->order_id = $id;



        $this->wandSupliers = Supliers::where('toepassing_wand', 1)->get();
        $this->dakSupliers = Supliers::where('toepassing_dak', 1)->get();
        $this->panelTypes = PanelType::whereIn('id', PriceRules::pluck('panel_type'))->get();
        $this->order = Order::where('id', $id)->first();
        $this->creator_user_id = $this->order->user_id;

        $this->creator_user = User::where('id', $this->creator_user_id)->first();
        $this->company = Company::where('id', $this->creator_user->bedrijf_id)->first();
        $this->companyDiscount = $this->company->discount;

        $this->exsistingOrderLines = OrderLines::where('order_id', $id)->get();

        $this->werkendeBreedte = $this->dakSupliers->first()->werkende_breedte;
        $this->brands = $this->dakSupliers;

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

        $this->priceRule = PanelType::where('name', $this->kerndikte)->first()->priceRule;
        $this->priceRulePrice = $this->priceRule->price;

        foreach($this->exsistingOrderLines as $key => $exsistingOrderLine) {
            $this->orderLines[] = $key;
            $this->fillCb[$key] = $exsistingOrderLine->fillCb;
            $this->fillTotaleLengte[$key] = $exsistingOrderLine->fillTotaleLengte;
            $this->aantal[$key] = $exsistingOrderLine->aantal;
            $this->m2[$key] = $exsistingOrderLine->m2;
            $this->cb[$key] = $exsistingOrderLine->fillCb;
            $this->totaleLengte[$key] = $exsistingOrderLine->fillTotaleLengte;
            $this->panelValues[$key] = [
                1 => $exsistingOrderLine->lb ?? 0,
                2 => $exsistingOrderLine->fillCb ?? 0,
                3 => $exsistingOrderLine->nokafschuining ?? 0,
                '4_1' => $exsistingOrderLine->vrije_ruimte_1 ?? 0,
                '4_2' => $exsistingOrderLine->vrije_ruimte_2 ?? 0,
            ];

            // ✅ Vul selectedPanelOption op basis van welke waarden groter dan 0 zijn
            $this->selectedPanelOption[$key] = [];
            if($exsistingOrderLine->lb > 0) $this->selectedPanelOption[$key][] = 1;
            if($exsistingOrderLine->fillCb > 0) $this->selectedPanelOption[$key][] = 2;
            if($exsistingOrderLine->nokafschuining > 0) $this->selectedPanelOption[$key][] = 3;
            if($exsistingOrderLine->vrije_ruimte_1 > 0 || $exsistingOrderLine->vrije_ruimte_2 > 0) $this->selectedPanelOption[$key][] = 4;

            if(empty($this->selectedPanelOption[$key])) {
                $this->panelImages[$key] = '/storage/images/rietpanel/paneel.png';
            } else {
                $options = $this->selectedPanelOption[$key];
                sort($options);
                $keyString = implode('-', $options);
                $this->panelImages[$key] = "/storage/images/rietpanel/paneel-$keyString.png";
            }

        }

        if ($this->order->status == 'Bevestigd') {
            $this->showConfirmModal = true;
        }

        if($this->order->status == 'Bevestigd') {
            $this->confirmedOrder = true;
        }


        if(Auth::user()->is_admin || !Auth::user()->is_architect) {
            return view('livewire.orders.orders');
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
        return view('livewire.orders.changeOrder');
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


    public function updatePanelValues($key,$index)
    {
        $this->panelValues[$key][$index] = $this->panelValues[$key][$index];
    }

    public function showLines() {
        dd($this->orderLines);
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

    public function addOrderLine() {
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
            3=> 0,
            '4_1' => 0,
            '4_2' => 0
        ];
        $this->panelImages[] = '/storage/images/rietpanel/paneel.png';
        $this->selectedPanelOption[] = [];


    }

    public function removeOrderLine($index)
    {
        if(isset($this->exsistingOrderLines[$index])) {
            $this->deletedOrderLines[] = $this->exsistingOrderLines[$index]->id;
        }

        // Verwijder dezelfde index uit ALLE arrays
        unset($this->orderLines[$index]);
        unset($this->totaleLengte[$index]);
        unset($this->aantal[$index]);
        unset($this->lb[$index]);
        unset($this->cb[$index]);
        unset($this->fillCb[$index]);
        unset($this->fillLb[$index]);
        unset($this->fillTotaleLengte[$index]);
        unset($this->m2[$index]);
        unset($this->panelValues[$index]);
        unset($this->selectedPanelOption[$index]);
        unset($this->panelImages[$index]);

        // Herindexeer alle arrays om consistent te blijven
        $this->orderLines = array_values($this->orderLines);
        $this->totaleLengte = array_values($this->totaleLengte);
        $this->aantal = array_values($this->aantal);
        $this->lb = array_values($this->lb);
        $this->cb = array_values($this->cb);
        $this->fillCb = array_values($this->fillCb);
        $this->fillLb = array_values($this->fillLb);
        $this->fillTotaleLengte = array_values($this->fillTotaleLengte);
        $this->m2 = array_values($this->m2);
        $this->panelValues = array_values($this->panelValues);
        $this->selectedPanelOption = array_values($this->selectedPanelOption);
        $this->panelImages = array_values($this->panelImages);
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
            'fillTotaleLengte.*' => 'required|numeric|min:500|max:17000',
            'aantal.*' => 'required|numeric|min:1',
            'requested_delivery_date' => 'required',
        ];

        if (!empty($this->selectedPanelOption)) {

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
            'cb.*.max' => __('messages.De CB mag maximaal 140mm zijn'),
            'cb.*.min' => __('messages.De CB moet minimaal 20mm zijn'),
            'lb.*.min' => __('messages.De LB moet minimaal 20mm zijn'),
            'lb.*.max' => __('messages.De LB mag maximaal 210mm zijn'),
            'panelValues.*.3.min' =>  __('messages.De nokafschuining moet minimaal 0 graden zijn'),
            'panelValues.*.3.max' =>  __('messages.De nokafschuining mag maximaal 60 graden zijn'),
            'kerndikte' => __('messages.De kerndikte is een verplicht veld'),
            'panelValues.*.4_1.min' =>  __('messages.Dit moet een getal hoger dan 300 mm zijn'),
            'panelValues.*.4_2.min' =>  __('messages.Dit moet een getal hoger dan 50 mm zijn'),

            'requested_delivery_date.required' => __('messages.Dit is een verplicht veld'),
        ];
    }

    public function saveOrder() {


        $this->fillTotaleLengte = array_filter($this->fillTotaleLengte, fn($v) => $v !== '');
        $this->fillTotaleLengte = array_values($this->fillTotaleLengte);


        $this->validate();



        Order::where('id', $this->order_id)->update([
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

        $order = Order::where('id', $this->order_id)->first();



        OrderLines::where('order_id', $this->order_id)->delete();


        foreach($this->orderLines as $index => $key) {

            $fillLb = array_key_exists($index, $this->fillLb) ? $this->fillLb[$index] : '0';
            $fillTotaleLengte = array_key_exists($index, $this->fillTotaleLengte) ? $this->fillTotaleLengte[$index] : '0';
            $aantal = array_key_exists($index, $this->aantal) ? $this->aantal[$index] : '0';
            $m2 = array_key_exists($index, $this->m2) ? $this->m2[$index] : '0';
            $selectedOptions = $this->selectedPanelOption[$index] ?? [];



            OrderLines::create([
                'order_id' => $order->id,
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

        $orderLines = OrderLines::where('order_id', $order->id)->get();
        $user = User::where('id', $order->user_id)->first();

        $showNokafschuining = $orderLines->where('nokafschuining', '>', 0)->count() > 0;
        $showVrijeRuimte = $orderLines->where('vrije_ruimte_2', '>', 0)->count() > 0;
        $showCb = $orderLines->where('fillCb', '>', 0)->count() > 0;
        $showLb = $orderLines->where('lb', '>', 0)->count() > 0;

        Pdf::loadView('pdf.order', ['order' => $order, 'orderLines' => $orderLines, 'showNokafschuining' => $showNokafschuining, 'showLb' => $showLb, 'showCb' => $showCb, 'showVrijeRuimte' => $showVrijeRuimte])->save(public_path('/storage/orders/order-' . $order->order_id . '.pdf'));


        if(Auth::user()->is_admin == 1 && $order->user_id != Auth::user()->id) {
            Mail::to($user->email)->send(new orderUpdated($order));
        }

        session()->flash('success','De order is bewerkt.');
        return $this->redirect('/orders', navigate: true);
    }

    public function cancelChangeOrder() {
        return $this->redirect('/orders', navigate: true);
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
