<?php

namespace App\Livewire\Orders;

use App\Mail\newOrderCustomer;
use App\Mail\orderRemoved;
use App\Mail\sendNewCustomer;
use App\Mail\sendOrder;
use App\Mail\sendOrderConfirmed;
use App\Models\Order;
use App\Models\OrderLines;
use App\Models\OrderTemplate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Livewire\Component;
use Spatie\LaravelPdf\Facades\Pdf;

class RemoveOrders extends Component
{

    public $orderId;

    public $order;

    public function mount() {
        if(Auth::user()->is_admin) {
            return view('livewire.orders.removeOrder');
        } else {
            session()->flash('error','U heeft geen rechten voor deze pagina');
            return $this->redirect('/dashboard', navigate: true);
        }
    }
    public function render()
    {
        $this->orderId = Route::current()->parameter('id');

        $this->order = Order::where('id', $this->orderId)->first();

        return view('livewire.orders.removeOrder');
    }

   public function deleteOrder($id) {
        Order::where('id', $id)->delete();
        OrderLines::where('order_id', $id)->delete();

       session()->flash('success','De order #'.$this->order->order_id.' is verwijderd');
       Mail::to(env('MAIL_TO_ADDRESS'))->send(new orderRemoved($this->order));

       return $this->redirect('/orders', navigate: true);


   }

   public function cancelDeleteOrder() {
        return $this->redirect('/orders', navigate: true);
    }
}
