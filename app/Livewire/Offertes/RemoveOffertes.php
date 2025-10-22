<?php

namespace App\Livewire\Offertes;

use App\Mail\sendNewCustomer;
use App\Mail\sendOrder;
use App\Mail\sendOrderConfirmed;
use App\Models\Offerte;
use App\Models\OfferteLines;
use App\Models\Order;
use App\Models\OrderLines;
use App\Models\OrderTemplate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Livewire\Component;
use Spatie\LaravelPdf\Facades\Pdf;

class RemoveOffertes extends Component
{

    public $offerteId;

    public $offerte;

    public function mount() {
        if(Auth::user()->is_admin) {
            return view('livewire.offertes.removeOfferte');
        } else {
            session()->flash('error','U heeft geen rechten voor deze pagina');
            return $this->redirect('/dashboard', navigate: true);
        }
    }

    public function render()
    {
        $this->offerteId = Route::current()->parameter('id');

        $this->offerte = Offerte::where('id', $this->offerteId)->first();

        return view('livewire.offertes.removeOfferte');
    }

   public function updateOrder($id) {

       Offerte::where('id', $id)->delete();
       OfferteLines::where('offerte_id', $id)->delete();

       session()->flash('success','De offerte #'.$this->offerte->offerte_id.' is verwijderd');

        Order::where('id', $id)->update([
            'status' => 'Bevestigd'
        ]);

       Mail::to($this->order->user->email)->send(new sendOrderConfirmed($this->order));

       return $this->redirect('/offertes', navigate: true);
   }

   public function cancelRemoveOfferte() {
        return $this->redirect('/offertes', navigate: true);
    }
}
