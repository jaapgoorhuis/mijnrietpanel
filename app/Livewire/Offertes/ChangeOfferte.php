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
        return view('livewire.orders.changeOrder');
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

    public function addOrderLine()
    {
        $index = count($this->orderLines);

        $this->orderLines[$index] = $index;
        $this->fillCb[$index] = 0;
        $this->cb[$index] = 0;
        $this->m2[$index] = 0;
        $this->lb[$index] = 0;
        $this->fillLb[$index] = 0;
        $this->totaleLengte[$index] = 0;
        $this->fillTotaleLengte[$index] = 0;
        $this->aantal[$index] = 1;
    }



    public function removeOrderLine($index) {
        unset($this->orderLines[$index]);
        unset($this->totaleLengte[$index]);
        unset($this->aantal[$index]);
        unset($this->lb[$index]);
        unset($this->cb[$index]);
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
            'fillTotaleLengte.*' => 'required|numeric|min:500|max:14500',
            'aantal.*' => 'required|numeric|min:1',
            'requested_delivery_date' => 'required',

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

    public function saveOrder() {

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

        $order = Order::find($this->order_id);
        OrderLines::where('order_id', $this->order_id)->delete();

        foreach($this->orderLines as $index => $key) {

            $fillCb = array_key_exists($index, $this->fillCb) ? $this->fillCb[$index] : '0';
            $fillLb = array_key_exists($index, $this->fillLb) ? $this->fillLb[$index] : '0';
            $fillTotaleLengte = array_key_exists($index, $this->fillTotaleLengte) ? $this->fillTotaleLengte[$index] : '0';
            $aantal = array_key_exists($index, $this->aantal) ? $this->aantal[$index] : '0';
            $m2 = array_key_exists($index, $this->m2) ? $this->m2[$index] : '0';

            OrderLines::create([
                'order_id' => $order->id,
                'fillCb' => $fillCb,
                'fillLb' => $fillLb,
                'fillTotaleLengte' => $fillTotaleLengte,
                'aantal' => $aantal,
                'user_id' => $this->creator_user_id,
                'm2' => $m2
            ]);
        }

        $orderLines = OrderLines::where('order_id', $order->id)->get();
        $user = User::where('id', $order->user_id)->first();

        Pdf::loadView('pdf.order',['order' => $order, 'orderLines' => $orderLines])->save(public_path('/storage/orders/order-'.$order->order_id.'.pdf'));

        // dees krijgen we latijd natuurlijk

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
