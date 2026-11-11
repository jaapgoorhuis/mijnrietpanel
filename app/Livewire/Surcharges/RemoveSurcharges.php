<?php

namespace App\Livewire\Surcharges;

use App\Mail\sendUpdatedUser;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class RemoveSurcharges extends Component
{
    public $surcharge;


    public function mount($id) {
        $this->surcharge = \App\Models\Surcharges::where('id', $id)->first();

    }
    public function render()
    {
        if(Auth::user()->is_admin) {
            return view('livewire.surcharges.removeSurcharges');
        } else {
            return $this->redirect('/dashboard', navigate: true);
        }
    }
    public function cancelRemoveSurcharge() {
        return $this->redirect('/surcharges', navigate: true);
    }

    public function deleteSurcharge($id) {
        $surcharge = \App\Models\Surcharges::where('id', $id)->first();

        DB::transaction(function () use ($surcharge, $id) {
            if ($surcharge) {
                $this->removeFromOpenDocuments($surcharge);
            }

            \App\Models\Surcharges::where('id', $id)->delete();
        });

        session()->flash('success','De toeslag is verwijderd');
        return $this->redirect('/surcharges', navigate: true);
    }

    /**
     * Haalt de verwijderde toeslag ook weg bij offertes en niet-bevestigde orders
     * en werkt hun totalen bij. Bevestigde orders blijven ongewijzigd.
     */
    private function removeFromOpenDocuments($surcharge): void
    {
        // Orders (niet bevestigd)
        $orderIds = DB::table('order_surcharges')
            ->join('orders', 'orders.id', '=', 'order_surcharges.order_id')
            ->where('order_surcharges.rule', $surcharge->rule)
            ->where('order_surcharges.name', $surcharge->name)
            ->where('orders.status', '!=', 'Bevestigd')
            ->pluck('order_surcharges.order_id')
            ->unique();

        foreach ($orderIds as $orderId) {
            DB::table('order_surcharges')
                ->where('order_id', $orderId)
                ->where('rule', $surcharge->rule)
                ->where('name', $surcharge->name)
                ->delete();

            $this->recalculateTotals('orders', 'order_surcharges', 'order_id', $orderId, true);
        }

        // Offertes
        $offerteIds = DB::table('offerte_surcharges')
            ->where('rule', $surcharge->rule)
            ->where('name', $surcharge->name)
            ->pluck('offerte_id')
            ->unique();

        foreach ($offerteIds as $offerteId) {
            DB::table('offerte_surcharges')
                ->where('offerte_id', $offerteId)
                ->where('rule', $surcharge->rule)
                ->where('name', $surcharge->name)
                ->delete();

            $this->recalculateTotals('offerte', 'offerte_surcharges', 'offerte_id', $offerteId, false);
        }
    }

    private function recalculateTotals(string $table, string $surchargeTable, string $fk, $id, bool $hasRules): void
    {
        $document = DB::table($table)->where('id', $id)->first();

        if (!$document) {
            return;
        }

        $surchargesTotal = (float) DB::table($surchargeTable)->where($fk, $id)->sum('total');
        $rulePrice = $hasRules ? (float) DB::table('order_rules')->where('order_id', $id)->sum('price') : 0;

        $vatBase = (float) $document->subtotal + $surchargesTotal + $rulePrice;
        $vat = round($vatBase * 0.21, 2);

        DB::table($table)->where('id', $id)->update([
            'surcharges_total' => $surchargesTotal,
            'vat_total' => $vat,
            'grand_total' => round($vatBase + $vat, 2),
        ]);
    }

}
