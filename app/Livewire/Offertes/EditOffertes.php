<?php

namespace App\Livewire\Offertes;

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

class EditOffertes extends Component
{

    public $orderId;

    public $order;

    public function mount() {
        if(Auth::user()->is_admin) {
            return view('livewire.orders.editOffertes');
        } else {
            session()->flash('error','U heeft geen rechten voor deze pagina');
            return $this->redirect('/dashboard', navigate: true);
        }
    }

    public function render()
    {
        $this->orderId = Route::current()->parameter('id');

        $this->order = Order::where('id', $this->orderId)->first();

        return view('livewire.orders.editOffertes');
    }

   public function updateOrder($id) {
        Order::where('id', $id)->update([
            'status' => 'Bevestigd'
        ]);

       Mail::to($this->order->user->email)->send(new sendOrderConfirmed($this->order));


       session()->flash('success','De order #'.$this->order->order_id.' is bevestigd. Er is een email verstuurd met een bevestiging naar '.$this->order->user->email);

       return $this->redirect('/orders', navigate: true);
   }

   public function cancelUpdateOrder() {
        return $this->redirect('/orders', navigate: true);
    }
}
