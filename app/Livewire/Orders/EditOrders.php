<?php

namespace App\Livewire\Orders;

use App\Mail\sendNewCustomer;
use App\Mail\sendOrder;
use App\Mail\sendOrderConfirmed;
use App\Models\Order;
use App\Models\OrderLines;
use App\Models\OrderRules;
use App\Models\OrderTemplate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Livewire\Component;
use Spatie\LaravelPdf\Facades\Pdf;

class EditOrders extends Component
{

    public $orderId;

    public $order;

    public $price;
    public $rule;
    public $show_orderlist = false;

    public $delivery_date;

    public function mount() {


        if(Auth::user()->is_admin) {
            $this->orderId = Route::current()->parameter('id');

            $this->order = Order::where('id', $this->orderId)->first();
            return view('livewire.orders.editOrder');

        } else {
            session()->flash('error','U heeft geen rechten voor deze pagina');
            return $this->redirect('/dashboard', navigate: true);
        }


    }
    public function render()
    {


        return view('livewire.orders.editOrder');
    }

    public function rules()
    {
        $rules = [
            'rule' => 'required',
            'price' => 'required',
         ];

        return $rules;
    }

    public function rules2()
    {
        $rules = [
            'delivery_date' => 'required',

        ];

        return $rules;
    }

    public function messages(): array
    {
        return [
            'rule.required' => 'De regel is een verplicht veld.',
            'price.required' => 'De prijs is een verplicht veld.',
            'price.numeric' => 'De prijs mag alleen een nummer zijn.',
            'delivery_date.required' => 'Selecteer een leverdatum.'

        ];
    }

   public function updateOrder($id) {

        if($this->rule || $this->price) {
            $this->validate($this->rules());


            OrderRules::create(
                [
                    'order_id' => $this->orderId,
                    'rule' => $this->rule,
                    'price' => $this->price,
                    'show_orderlist' => $this->show_orderlist,
                ]
            );
        }

        $this->validate($this->rules2());


        Order::where('id', $id)->update([
            'status' => 'Bevestigd',
            'delivery_date' => $this->delivery_date,
        ]);



       Mail::to($this->order->user->email)->send(new sendOrderConfirmed($this->order));


       session()->flash('success','De order #'.$this->order->order_id.' is bevestigd. Er is een email verstuurd met een bevestiging naar '.$this->order->user->email);

       return $this->redirect('/orders', navigate: true);
   }

   public function cancelUpdateOrder() {
        return $this->redirect('/orders', navigate: true);
    }
}
