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
        $order = Order::with([
            'orderLines.waterstops',
            'orderRules',
        ])
            ->where('order_id', $order_id)
            ->firstOrFail();
        $orderlines = $order->orderLines;

        $dikte = intval(str_replace( 'mm', '', $order->kerndikte)) + 30;

        $maxDikte = 1220;

        if (empty($orderlines)) {
            abort(404, 'Geen orderlines gevonden voor deze order.');
        }

        // -----------------------------
        // Pakketlogica (zoals eerder)
        // -----------------------------
        $groepen = [];
        foreach ($orderlines as $line) {
            $groepen[$line->fillTotaleLengte][] = $line;
        }
        krsort($groepen);

        $pakketten = [];
        $leftovers = [];

        foreach ($groepen as $lengte => $lines) {

            $items = [];

            foreach ($lines as $l) {

                for ($i = 0; $i < $l->aantal; $i++) {

                    $items[] = [
                        'orderLine' => $l,
                        'dikte' => $dikte,
                    ];

                }
            }

            $diktePerItem = $items[0]['dikte'];
            $perPakket = intdiv($maxDikte, $diktePerItem);
            $vollPakketten = intdiv(count($items), $perPakket);

            for ($i = 0; $i < $vollPakketten; $i++) {

                $pakket = array_splice($items, 0, $perPakket);

                usort($pakket, function ($a, $b) {
                    return $b['orderLine']->fillTotaleLengte <=> $a['orderLine']->fillTotaleLengte;
                });

                $pakketten[] = $pakket;
            }

            if (count($items)) {
                $leftovers = array_merge($leftovers, $items);
            }
        }

        usort($leftovers, function ($a, $b) {
            return $b['orderLine']->fillTotaleLengte <=> $a['orderLine']->fillTotaleLengte;
        });
        $current = [];
        $currentDikte = 0;
        foreach ($leftovers as $item) {
            if ($currentDikte + $item['dikte'] > $maxDikte) {
                usort($current, function ($a, $b) {
                    return $b['orderLine']->fillTotaleLengte <=> $a['orderLine']->fillTotaleLengte;
                });
                $pakketten[] = $current;
                $current = [];
                $currentDikte = 0;
            }
            $current[] = $item;
            $currentDikte += $item['dikte'];
        }
        if (!empty($current)) {

            usort($current, function ($a, $b) {
                return $b['orderLine']->fillTotaleLengte <=> $a['orderLine']->fillTotaleLengte;
            });

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
