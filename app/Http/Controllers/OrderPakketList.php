<?php

namespace App\Http\Controllers;

use App\Models\OrderLines;
use App\Models\Order;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use function Webmozart\Assert\Tests\StaticAnalysis\integer;

class OrderPakketList extends Controller
{
    public function generatePdf($order_id)
    {
        $order = Order::where('order_id', $order_id)->first();

        $dikte = intval(str_replace( 'mm', '', $order->kerndikte)) + 30;

        $orderlines = $order->orderLines->toArray();
        $maxDikte = 1220;

        if (empty($orderlines)) {
            abort(404, 'Geen orderlines gevonden voor deze order.');
        }

        // -----------------------------
        // Pakketlogica (zoals eerder)
        // -----------------------------
        $groepen = [];
        foreach ($orderlines as $line) {
            $groepen[$line['fillTotaleLengte']][] = $line;
        }
        krsort($groepen);

        $pakketten = [];
        $leftovers = [];

        foreach ($groepen as $lengte => $lines) {
            $items = [];
            foreach ($lines as $l) {
                if(!$l['fillCb']) {
                    $cb = 0;
                } else {
                    $cb = $l['fillCb'];
                }

                for ($i = 0; $i < $l['aantal']; $i++) {
                    $items[] = [
                        'id' => $l['id'],
                        'dikte' => $dikte,
                        'lengte' => $lengte,
                        'cb' => $cb,
                    ];
                }
            }

            $diktePerItem = $items[0]['dikte'];
            $perPakket = intdiv($maxDikte, $diktePerItem);
            $vollPakketten = intdiv(count($items), $perPakket);

            for ($i = 0; $i < $vollPakketten; $i++) {
                $pakket = array_splice($items, 0, $perPakket);
                usort($pakket, fn($a, $b) => $b['lengte'] <=> $a['lengte']);
                $pakketten[] = $pakket;
            }

            if (count($items) > 0) {
                $leftovers = array_merge($leftovers, $items);
            }
        }

        usort($leftovers, fn($a, $b) => $b['lengte'] <=> $a['lengte']);
        $current = [];
        $currentDikte = 0;
        foreach ($leftovers as $item) {
            if ($currentDikte + $item['dikte'] > $maxDikte) {
                usort($current, fn($a, $b) => $b['lengte'] <=> $a['lengte']);
                $pakketten[] = $current;
                $current = [];
                $currentDikte = 0;
            }
            $current[] = $item;
            $currentDikte += $item['dikte'];
        }
        if (!empty($current)) {
            usort($current, fn($a, $b) => $b['lengte'] <=> $a['lengte']);
            $pakketten[] = $current;
        }

        // -----------------------------
        // PDF genereren en tonen in browser
        // -----------------------------
        $pdf = PDF::loadView('pdf.pakketlijst', [
            'order' => $order,
            'pakketten' => $pakketten
        ]);

        $filename = 'pakketlijst-' . $order_id . '.pdf';

        return $pdf->stream($filename); // opent direct in nieuw tabblad
    }
}
