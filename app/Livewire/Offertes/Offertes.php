<?php

namespace App\Livewire\Offertes;

use App\Mail\sendOrder;
use App\Models\Offerte;
use App\Models\OfferteLines;
use App\Models\Order;
use App\Models\OrderLines;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use function Spatie\LaravelPdf\Support\pdf;

class Offertes extends Component
{
    public $offertes;
    public $editOfferteId;


    public function mount() {
        if(Auth::user()->is_admin || !Auth::user()->is_architect) {
            return view('livewire.offertes.offertes');
        } else {
            session()->flash('error','U heeft geen rechten voor deze pagina');
            return $this->redirect('/dashboard', navigate: true);
        }
    }
    public function render()
    {
        if(Auth::user()->is_admin) {
            $this->offertes = Offerte::get();
        }
        else if(Auth::user()->companys->is_reseller) {
            $this->offertes = Offerte::whereHas('user', function ($query) {
                $query->where('bedrijf_id', Auth::user()->bedrijf_id);
            })->get();
        } else {
            $this->offertes = Offerte::where('user_id', Auth::user()->id)->get();
        }


        return view('livewire.offertes.offertes');
    }

    public function newOfferte()
    {
        if (Auth::user()->bedrijf_id == 0) {
            session()->flash('error', 'Uw account is niet gekoppeld aan een bedrijf. Neem contact met rietpanel op om dit probleem te verhelpen.');
            return $this->redirect('/offertes', navigate: true);
        } else {
            return $this->redirect('/offertes/create', navigate: true);
        }
    }

    public function createOfferteOrder($offerteId) {
        $offerte = Offerte::where('id', $offerteId)->first();

        $offerte->update([
            'is_order' => 1
        ]);
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
            'klantnaam' => $offerte->klantnaam,
            'referentie' => $offerte->referentie,
            'aflever_straat' => $offerte->aflever_straat,
            'aflever_postcode' => $offerte->aflever_postcode,
            'aflever_land' => $offerte->aflever_land,
            'aflever_plaats' => $offerte->aflever_plaats,
            'project_naam' => $offerte->project_naam,
            'rietkleur' => $offerte->rietkleur,
            'toepassing' => $offerte->toepassing,
            'merk_paneel' => $offerte->merk_paneel,
            'kerndikte' => $offerte->kerndikte,
            'intaker' => $offerte->intaker,
            'user_id' => Auth::user()->id,
            'status' => 'In behandeling',
            'order_id' => $orderId,
            'discount' => $offerte->discount
        ]);

        $orderAfterCreate = Order::orderBy('id', 'desc')->first();

        $offerteLines = OfferteLines::where('offerte_id',$offerte->id)->get();

        foreach($offerteLines as $offerteLine) {
            OrderLines::create([
                'order_id' => $orderAfterCreate->id,
                'rietkleur' => $offerteLine->rietkleur,
                'toepassing' => $offerteLine->toepassing,
                'merk_paneel' => $offerteLine->merk_paneel,
                'fillCb' => $offerteLine->fillCb,
                'fillLb' => $offerteLine->fillLb,
                'kerndikte' => $offerteLine->kerndikte,
                'fillTotaleLengte' => $offerteLine->fillTotaleLengte,
                'aantal' => $offerteLine->aantal,
                'user_id' => Auth::user()->id,
                'm2' => $offerteLine->m2
            ]);
        }

        $order = Order::orderBy('id', 'desc')->first();


        $orderLines = OrderLines::where('order_id', $order->id)->get();

        Pdf::loadView('pdf.order',['order' => $order, 'orderLines' => $orderLines])->save(public_path('/storage/orders/order-'.$orderId.'.pdf'));
        Mail::to(env('MAIL_TO_ADDRESS'))->send(new sendOrder($order));

        Mail::to(Auth::user()->email)->send(new sendOrder($order));

        session()->flash('success', 'Er is een order aangemaakt van deze offerte.');
        return $this->redirect('/offertes', navigate: true);
    }
}
