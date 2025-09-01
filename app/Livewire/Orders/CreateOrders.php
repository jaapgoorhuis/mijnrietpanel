<?php

namespace App\Livewire\Orders;

use App\Mail\sendOrder;
use App\Models\Order;
use App\Models\OrderLines;
use App\Models\OrderTemplate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Barryvdh\DomPDF\Facade\Pdf;

class CreateOrders extends Component
{

    public $project_naam;
    public $project_adres;
    public $intaker;

    public $rietkleur = [];
    public $toepassing = [];
    public $merk_paneel = [];
    public $aantal = [];
    public $kerndikte = [];

    public $m2 = [];

    public $fillTotaleLengte = ['0'];
    public $fillCb = ['0'];
    public $fillLb = ['0'];

    public $lb = [];
    public $cb = [];
    public $totaleLengte = [];


    public $brands = [];


    public $orderLines = [];

    public $orderLineValues = [];

    public $saved = FALSE;

    public function render()
    {
        return view('livewire.orders.createOrder');
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

    public function updateBrands($index) {
        if($this->toepassing[$index] == 'wand') {
            $this->brands[$index] = [
                'Kingspan' => '1000',
                'Joriside' => '1000',
                'SAB Profiel' => '1000',
                'FALK' => '1060'
            ];
        }else {
            $this->merk_paneel[$index] = 'Kingspan';
            $this->brands[$index] = [
                'Kingspan' => '1000'
            ];
        }

        $this->updateM2($index);
    }

    public function addOrderLine() {
        $this->orderLines[] = '';
        $this->fillCb[] = '0';
        $this->cb[] = '0';
        $this->m2[] = '0';
        $this->lb[] = '0';
        $this->fillLb[] = '0';
        $this->totaleLengte[] = '0';
        $this->fillTotaleLengte[] = '0';
        $this->aantal[] = '1';
        $this->merk_paneel[] = 'Kingspan';
        $this->brands[] = [

            'Kingspan' => '1000',
            'Joriside' => '1000',
            'SAB Profiel' => '1000',
            'FALK' => '1060'
        ];
    }

    public function removeOrderLine($index) {

        unset($this->orderLines[$index]);
        $this->orderLines = array_values($this->orderLines);
    }

    protected $rules = [
        'project_naam' => 'required',
        'intaker' => 'required',
        'totaleLengte.*' => 'required|numeric|min:1',
        'aantal.*' => 'required|numeric|min:1',
        'lb.*' => 'required|numeric|max:210',
        'cb.*' => 'required|numeric|max:200'
    ];

    public function messages(): array
    {
        return [
            'project_naam.required' => 'De projectnaam is een verplicht veld.',
            'intaker.required' => 'Vul aub uw naam in.',
            'totaleLengte.*.min' => 'De lengte moet mimimaal 1mm zijn.',
            'aantal.*.min' => 'Dit moet mimimaal 1 paneel zijn.',
            'cb.*.max' => 'De CB mag maximaal 200mm zijn.',
            'lb.*.max' => 'De LB mag maximaal 210mm zijn.',
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
            'project_naam' => $this->project_naam,
            'project_adres' => $this->project_adres,
            'intaker' => $this->intaker,
            'user_id' => Auth::user()->id,
            'status' => 'In behandeling',
            'order_id' => $orderId
        ]);

        $order = Order::orderBy('id', 'desc')->first();

        foreach($this->orderLines as $index => $key) {

            $rietkleur   = array_key_exists($index, $this->rietkleur)   ? $this->rietkleur[$index]   : 'Old look';
            $toepassing  = array_key_exists($index, $this->toepassing)  ? $this->toepassing[$index]  : 'Wand';
            $merk_paneel = array_key_exists($index, $this->merk_paneel) ? $this->merk_paneel[$index] : 'Kingspan';
            $fillCb = array_key_exists($index, $this->fillCb) ? $this->fillCb[$index] : '0';
            $fillLb = array_key_exists($index, $this->fillLb) ? $this->fillLb[$index] : '0';
            $kerndikte = array_key_exists($index, $this->kerndikte) ? $this->kerndikte[$index] : '60mm';
            $fillTotaleLengte = array_key_exists($index, $this->fillTotaleLengte) ? $this->fillTotaleLengte[$index] : '0';
            $aantal = array_key_exists($index, $this->aantal) ? $this->aantal[$index] : '0';
            $m2 = array_key_exists($index, $this->m2) ? $this->m2[$index] : '0';

            OrderLines::create([
                'order_id' => $order->id,
                'rietkleur' => $rietkleur,
                'toepassing' => $toepassing,
                'merk_paneel' => $merk_paneel,
                'fillCb' => $fillCb,
                'fillLb' => $fillLb,
                'kerndikte' => $kerndikte,
                'fillTotaleLengte' => $fillTotaleLengte,
                'aantal' => $aantal,
                'user_id' => Auth::user()->id,
                'm2' => $m2
            ]);
        }

        $orderLines = OrderLines::where('order_id', $order->id)->get();

        Pdf::loadView('pdf.order',['order' => $order, 'orderLines' => $orderLines])->save(public_path('/storage/orders/order-'.$orderId.'.pdf'));

        Mail::to(env('MAIL_TO_ADDRESS'))->send(new sendOrder($order));

        session()->flash('success','De order is aangemaakt. Wij controleren de order en zullen deze zo spoedig mogelijk bevestigen');
        return $this->redirect('/orders', navigate: true);
    }

    public function cancelCreateOrder() {
        return $this->redirect('/orders', navigate: true);
    }

    public function updateM2($index) {
           $werkendeBreedte = (int)$this->brands[$index][$this->merk_paneel[$index]];

           $lengtePaneel = (int)$this->fillTotaleLengte[$index];

           $werkendeBreedteM = $werkendeBreedte / 1000;
           $lengtePaneelM = $lengtePaneel / 1000;


           if($lengtePaneel == '0' || $werkendeBreedte == '0') {
               $this->m2[$index] = 0;
           }
           else {
               $this->m2[$index] = round($lengtePaneelM * $werkendeBreedteM * intval($this->aantal[$index]),2);
           }
    }
}
