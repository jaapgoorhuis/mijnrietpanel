<?php

namespace App\Livewire\Offertes;

use App\Mail\sendOfferteToOrder;
use App\Mail\sendOrder;
use App\Models\Offerte;
use App\Models\OfferteLines;
use App\Models\Order;
use App\Models\OrderLines;
use App\Models\OrderLineWaterstop;
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

            $this->offertes = Offerte::orderby('offerte_id', 'DESC')->get();
        }
        else if(Auth::user()->companys->is_reseller) {
            $this->offertes = Offerte::whereHas('user', function ($query) {
                $query->where('bedrijf_id', Auth::user()->bedrijf_id);
            })->OrderBy('offerte_id', 'asc')->get();
        } else {
            $this->offertes = Offerte::where('user_id', Auth::user()->id)->OrderBy('offerte_id', 'asc')->get();
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


    public function removeOfferte($id)
    {
        return $this->redirect('/offertes/remove/' . $id, navigate: true);
    }

    public function changeOfferte($id)
    {
        $offerte = Offerte::where('id', $id)->first();
        if($offerte->is_order == 0) {
            return $this->redirect('/offertes/edit/' . $id, navigate: true);
        } else {
            return $this->redirect('/offertes', navigate: true);
        }
    }


    public function uploadOrderForm() {
        if(Auth::user()->is_admin) {
            return $this->redirect('/offertes/upload', navigate: true);
        }
        else {
            return $this->redirect('/offertes', navigate: true);
        }
    }

    public function createOfferteOrder($offerteId)
    {
        $offerte = Offerte::with([
            'offerteLines.waterstops',
            'surcharges',
            'user',
        ])->findOrFail($offerteId);

        $offerte->update([
            'is_order' => 1,
        ]);

        $latestOrder = Order::orderBy('id', 'desc')->first();

        if ($latestOrder) {
            $currentYear = date('y');

            if (str_starts_with($latestOrder->order_id, $currentYear)) {
                $orderId = $latestOrder->order_id + 1;
            } else {
                $orderId = $currentYear . '0600';
            }
        } else {
            $orderId = 250600;
        }

        $order = Order::create([
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
            'discount' => $offerte->discount,
            'marge' => $offerte->marge ?? 0,
            'requested_delivery_date' => $offerte->requested_delivery_date,
            'comment' => $offerte->comment,
            'lang' => $offerte->lang,

            'subtotal' => $offerte->subtotal,
            'surcharges_total' => $offerte->surcharges_total,
            'vat_total' => $offerte->vat_total,
            'grand_total' => $offerte->grand_total,
            'prices_updated_at' => $offerte->prices_updated_at,
        ]);

        foreach ($offerte->offerteLines as $offerteLine) {

            $orderLine = OrderLines::create([
                'order_id' => $order->id,
                'rietkleur' => $offerteLine->rietkleur,
                'toepassing' => $offerteLine->toepassing,
                'merk_paneel' => $offerteLine->merk_paneel,
                'fillCb' => $offerteLine->fillCb,
                'fillLb' => $offerteLine->fillLb,
                'kerndikte' => $offerteLine->kerndikte,
                'fillTotaleLengte' => $offerteLine->fillTotaleLengte,
                'aantal' => $offerteLine->aantal,
                'user_id' => Auth::id(),
                'm2' => $offerteLine->m2,

                'lb' => $offerteLine->lb,
                'nokafschuining' => $offerteLine->nokafschuining,
                'vrije_ruimte_1' => $offerteLine->vrije_ruimte_1,
                'vrije_ruimte_2' => $offerteLine->vrije_ruimte_2,

                'waterstop_type' => $offerteLine->waterstop_type,
                'waterstop_vertical' => $offerteLine->waterstop_vertical,
                'waterstop_horizontal' => $offerteLine->waterstop_horizontal,

                'price_per_m2' => $offerteLine->price_per_m2,
            ]);

            foreach ($offerteLine->waterstops as $waterstop) {
                OrderLineWaterstop::create([
                    'order_line_id' => $orderLine->id,
                    'type' => $waterstop->type,
                    'vertical' => $waterstop->vertical,
                    'horizontal' => $waterstop->horizontal,
                ]);
            }
        }

        foreach ($offerte->surcharges as $surcharge) {
            \App\Models\OrderSurcharge::create([
                'order_id' => $order->id,
                'name' => $surcharge->name,
                'rule' => $surcharge->rule,
                'qty' => $surcharge->qty,
                'unit_price' => $surcharge->unit_price,
                'total' => $surcharge->total,
            ]);
        }

        $order->refresh();
        $order->load(['orderLines', 'user', 'surcharges']);

        $orderLines = $order->orderLines;

        $showNokafschuining = $orderLines->where('nokafschuining', '>', 0)->count() > 0;
        $showVrijeRuimte = $orderLines->where('vrije_ruimte_2', '>', 0)->count() > 0;
        $showCb = $orderLines->where('fillCb', '>', 0)->count() > 0;
        $showLb = $orderLines->where('lb', '>', 0)->count() > 0;

        $showWaterstop = $orderLines->contains(fn ($line) => $line->waterstops->count() > 0);
        $order->load(['orderLines.waterstops', 'user', 'surcharges']);

        Pdf::loadView('pdf.order', [
            'order' => $order,
            'orderLines' => $orderLines,
            'showNokafschuining' => $showNokafschuining,
            'showLb' => $showLb,
            'showCb' => $showCb,
            'showWaterstop' => $showWaterstop,
            'showVrijeRuimte' => $showVrijeRuimte,
        ])->save(public_path('/storage/orders/order-' . $orderId . '.pdf'));

        Mail::to(Auth::user()->email)->send(new sendOfferteToOrder($order));

        session()->flash('success', __('Er is een order aangemaakt van deze offerte'));

        return $this->redirect('/offertes', navigate: true);
    }
}
