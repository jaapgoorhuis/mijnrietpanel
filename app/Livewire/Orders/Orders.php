<?php

namespace App\Livewire\Orders;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use function Spatie\LaravelPdf\Support\pdf;

class Orders extends Component
{
    public $orders;
    public $editOrderId;

    public function render()
    {
        if(Auth::user()->is_admin) {
            $this->orders = Order::get();
        }else {
            $this->orders = Order::where('user_id', Auth::user()->id)->get();
        }


        return view('livewire.orders.orders');
    }

    public function newOrder() {
        if(Auth::user()->bedrijf_id == 0) {
            session()->flash('error','Uw account is niet gekoppeld aan een bedrijf. Neem contact met rietpanel op om dit probleem te verhelpen.');
            return $this->redirect('/orders', navigate: true);
        }else {
            return $this->redirect('/orders/create', navigate: true);
        }
    }

    public function download() {
        return pdf('pdf.order', [
            'invoiceNumber' => '1234',
            'customerName' => 'Grumpy Cat',
        ]);
    }


    public function editOrder($id)
    {
        if(Auth::user()->is_admin) {
            return $this->redirect('/orders/edit/' . $id, navigate: true);
        }
        else {
            return $this->redirect('/orders', navigate: true);
        }
    }

    public function uploadOrderForm() {
        if(Auth::user()->is_admin) {
            return $this->redirect('/orders/upload', navigate: true);
        }
        else {
            return $this->redirect('/orders', navigate: true);
        }
    }
}
