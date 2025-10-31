<?php

namespace App\Livewire\Orders;

use App\Mail\sendOrder;
use App\Mail\sendOrderList;
use App\Models\Company;
use App\Models\Offerte;
use App\Models\Order;
use App\Models\Supliers;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use function Spatie\LaravelPdf\Support\pdf;

class Orders extends Component
{
    public $orders;
    public $editOrderId;

    public function change() {
        dd('updated');
    }
    public function mount()
    {
        if(Auth::user()->is_admin) {
            $this->orders = Order::get();
        }
        else if(Auth::user()->companys->is_reseller) {
            $this->orders = Order::whereHas('user', function ($query) {
                $query->where('bedrijf_id', Auth::user()->bedrijf_id);
            })->get();
        } else {
            $this->orders  = Order::where('user_id', Auth::user()->id)->get();
        }

        if(Auth::user()->is_admin || !Auth::user()->is_architect) {
            return view('livewire.orders.orders');
        } else {
            session()->flash('error','U heeft geen rechten voor deze pagina');
            return $this->redirect('/dashboard', navigate: true);
        }
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

    public function removeOrder($id)
    {
        if(Auth::user()->is_admin) {
            return $this->redirect('/orders/remove/' . $id, navigate: true);
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

    public function changeOrder($id)
    {
        return $this->redirect('/orders/edit/' . $id, navigate: true);
    }


    public function SendOrderList($id) {
        $order = Order::where('id', $id)->first();
        $leverancier = Supliers::where('name', $order->merk_paneel)->first();

        Pdf::loadView('pdf.orderlijst',['order' => $order, 'leverancier'=> $leverancier])->save(public_path('/storage/orderlijst/order-'.$order->order_id.'.pdf'));

        try {
            if ($leverancier->suplier_email != '') {
                Mail::to(strtolower($leverancier->suplier_email))->send(new sendOrderList($order));
            } else {
                session()->flash('error','Er is geen email beschikbaar van deze leverancier.');
                return $this->redirect('/orders', navigate: true);
            }
        } catch (\Exception $e) {
            return redirect('/orders')->with('error', 'Er is een fout opgetreden bij het versturen van de e-mail.');
        }

        Order::where('id', $order->id)->update([
            'order_ordered' => date('d-m-Y')
        ]);

        session()->flash('success','De inkoop order is verstuurd.');
        return $this->redirect('/orders', navigate: true);
    }

}
