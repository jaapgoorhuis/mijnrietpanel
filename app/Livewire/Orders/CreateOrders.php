<?php

namespace App\Livewire\Orders;

use App\Mail\newOrderCustomer;
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
use App\Rules\ZeroOrMinFifty;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Barryvdh\DomPDF\Facade\Pdf;

class CreateOrders extends Component
{

    public $intaker;

    public $klant_naam;
    public $referentie;
    public $aflever_straat;
    public $aflever_postcode;
    public $aflever_plaats;
    public $aflever_land;

    public $rietkleur = 'Old look';
    public $toepassing = 'Dak';
    public $merk_paneel;
    public $aantal = [];
    public $kerndikte = '';

    public $m2 = [];
    public $discount = 0;

    public $fillTotaleLengte = [''];
    public $fillCb = [''];
    public $fillLb = [''];

    public $lb = [];
    public $cb = [];
    public $totaleLengte = [];

    public $panelBrands;
    public $panelTypes;
    public $panelApplications;
    public $panelLooks;


    public $brands = [];


    public $orderLines = [];

    public $orderLineValues = [];

    public $saved = FALSE;

    public $wandSupliers;
    public $dakSupliers;
    public $supliers;

    public $project_naam;

    public $priceRule;
    public $company;
    public $companyDiscount;
    public $werkendeBreedte;
    public $priceRulePrice;

    public $requested_delivery_date;

    public $marge = 0;

    public $locale;
    public $comment;

    public function mount() {
        $this->wandSupliers = Supliers::where('toepassing_wand', 1)->get();
        $this->dakSupliers = Supliers::where('toepassing_dak', 1)->get();
        $this->supliers = Supliers::get();

        $this->merk_paneel = $this->dakSupliers->first()->name;
        $this->werkendeBreedte = $this->dakSupliers->first()->werkende_breedte;

        $this->locale = config('app.locale'); // leest APP_LOCALE uit .env

        $this->company = Company::where('id', Auth::user()->bedrijf_id)->first();
        $this->companyDiscount = $this->company->discount;

        $this->priceRule = PriceRules::where('company_id', '0')->where('reseller', 0)->where('panel_type', '1')->first();
        $this->panelTypes = PanelType::whereIn('id', PriceRules::pluck('panel_type'))->get();

        $this->brands = $this->dakSupliers;
        if(Auth::user()->bedrijf_id == 0) {
            session()->flash('error', 'Uw account is niet gekoppeld aan een bedrijf. Hierdoor kunt u geen orders plaatsen. Neem contact met rietpanel op om dit probleem te verhelpen.');
            return $this->redirect('/orders', navigate: true);
        }

        if(Auth::user()->is_admin || !Auth::user()->is_architect) {
            return view('livewire.orders.createOrder');
        } else {
            session()->flash('error','U heeft geen rechten voor deze pagina');
            return $this->redirect('/dashboard', navigate: true);
        }


        $this->priceRulePrice = 0;

    }

    public function render()
    {
        return view('livewire.orders.createOrder');
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

    public function addOrderLine() {
        $this->orderLines[] = '';
        $this->fillCb[] = '';
        $this->cb[] = '0';
        $this->m2[] = '0';
        $this->lb[] = '0';
        $this->fillLb[] = '0';
        $this->totaleLengte[] = '';
        $this->fillTotaleLengte[] = '';
        $this->aantal[] = '';
    }

    public function removeOrderLine($index) {
        unset($this->orderLines[$index]);
        unset($this->totaleLengte[$index]);
        unset($this->aantal[$index]);
        unset($this->lb[$index]);
        unset($this->cb[$index]);
        $this->orderLines = array_values($this->orderLines);
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
            'totaleLengte.*' => 'required|numeric|min:500|max:14500',
            'aantal.*' => 'required|numeric|min:1',
            'kerndikte' => 'required',
            'requested_delivery_date' => 'required',

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

        $latestOrder = Order::orderBy('id', 'desc')->first();

        if($latestOrder) {
            $currentYear = date('y');
            if(str_starts_with($latestOrder->order_id, $currentYear)) {
                $orderId = $latestOrder->order_id + 1;
            } else {
                $orderId = $currentYear.'0600';
            }

        } else {
            $orderId = 250600;
        }
        Order::create([
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
            'status' => 'In behandeling',
            'order_id' => $orderId,
            'marge' => $this->marge,
            'requested_delivery_date' => $this->requested_delivery_date,
            'comment' => $this->comment,
            'lang' => $this->locale,
        ]);

        $order = Order::orderBy('id', 'desc')->first();

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
                'user_id' => Auth::user()->id,
                'm2' => $m2
            ]);
        }

        $orderLines = OrderLines::where('order_id', $order->id)->get();

        Pdf::loadView('pdf.order',['order' => $order, 'orderLines' => $orderLines])->save(public_path('/storage/orders/order-'.$orderId.'.pdf'));

        Mail::to(env('MAIL_TO_ADDRESS'))->send(new sendOrder($order));

        Mail::to(Auth::user()->email)->send(new newOrderCustomer($order));

        session()->flash('success',__('messages.De order is aangemaakt. Wij controleren de order en zullen deze zo spoedig mogelijk bevestigen'));
        return $this->redirect('/orders', navigate: true);
    }

    public function cancelCreateOrder() {
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
