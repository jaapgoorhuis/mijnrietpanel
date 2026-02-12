<?php

namespace App\Livewire\Orders;

use App\Mail\sendNewCustomer;
use App\Mail\sendOrder;
use App\Mail\sendOrderConfirmed;
use App\Mail\sendOrderList;
use App\Mail\sendOrderListConfirmation;
use App\Models\Order;
use App\Models\OrderLines;
use App\Models\OrderRules;
use App\Models\OrderTemplate;
use App\Models\Supliers;
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
    public $currentModal = 'first';
    public $delivery_date;

    public $send_copy = false;

    public $existing_purchage_order_email;
    public $existing_purchage_order_suplier;
    public $new_purchage_order_email;

    public $admin_email = 'administratie@rietpanel.nl';

    public function mount() {


        if(Auth::user()->is_admin) {
            $this->orderId = Route::current()->parameter('id');

            $this->order = Order::where('id', $this->orderId)->first();

            $this->existing_purchage_order_email = $this->order->Suplier->suplier_email;
            $this->existing_purchage_order_suplier = $this->order->Suplier->suplier_name;
            $this->new_purchage_order_email = $this->order->Suplier->suplier_email;



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

 public function NextModal($id)
    {
        if($this->rule || $this->price) {
            $this->validate($this->rules());
        }

        $this->validate($this->rules2());

        // Open de volgende modal
        $this->currentModal = 'next';
    }

    public function cancelNextModal() {
        $this->currentModal = 'first';
    }

    public function closeModal()
    {
        $this->currentModal = null;
    }


   public function updateOrder($id) {


       if($this->rule || $this->price) {
           OrderRules::create(
               [
                   'order_id' => $this->orderId,
                   'rule' => $this->rule,
                   'price' => $this->price,
                   'show_orderlist' => $this->show_orderlist,
               ]
           );
       }

       \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.orderlijst',['order' => $this->order, 'leverancier'=> $this->order->Suplier])->save(public_path('/storage/orderlijst/order-'.$this->order->order_id.'.pdf'));

       try {
           Mail::to(strtolower($this->new_purchage_order_email))->send(new sendOrderList($this->order));

       } catch (\Exception $e) {
           return redirect('/orders')->with('error', 'Er is een fout opgetreden bij het versturen van de e-mail naar de leverancier.' . $e);
       }

       Order::where('id', $id)->update([
           'status' => 'Bevestigd',
           'delivery_date' => $this->delivery_date,
           'order_ordered' => date('d-m-Y')
       ]);

       try {
           if($this->send_copy) {
               //send confirmation mail to administratie@rietpanel.nl
               Mail::to(strtolower($this->admin_email))->send(new sendOrderListConfirmation($this->order, $this->new_purchage_order_email));
           }

       } catch (\Exception $e) {
           return redirect('/orders')->with('error', 'Er is een fout opgetreden bij het versturen van de e-mail naar de administratie.' . $e);
       }

       try {
           Mail::to($this->order->user->email)->send(new sendOrderConfirmed($this->order));

       } catch (\Exception $e) {
           return redirect('/orders')->with('error', 'Er is een fout opgetreden bij het versturen van de e-mail naar de klant.' . $e);
       }


       //send mail to customer



       session()->flash('success','De order #'.$this->order->order_id.' is bevestigd. Er is een email verstuurd met een bevestiging naar '.$this->order->user->email.'. De inkooporder is verstuurd naar '.$this->new_purchage_order_email);

       return $this->redirect('/orders', navigate: true);
   }

   public function cancelUpdateOrder() {
        return $this->redirect('/orders', navigate: true);
    }
}
