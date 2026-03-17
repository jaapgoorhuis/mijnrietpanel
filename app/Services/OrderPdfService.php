<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderLines;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderPdfService
{
    /**
     * Genereer PDF voor een order
     * @param Order $order
     * @param string $type 'order', 'pakketlijst', 'fabriekslijst'
     * @return string output PDF content
     */
    public static function generatePdf(Order $order, string $type = 'order'): string
    {
        switch ($type) {
            case 'pakketlijst':
                $view = 'pdf.pakketlijst';
                $data = [
                    'order' => $order,
                    'pakketten' => self::generatePakketten($order)
                ];
                break;

            case 'fabriekslijst':
                $view = 'pdf.fabriekslijst';
                $data = [
                    'order' => $order,
                    'orderLines' => $order->orderLines
                ];
                break;

            case 'order':
            default:
                $view = 'pdf.order';
                $orderLines = OrderLines::where('order_id', $order->id)->get();
                $data = [
                    'order' => $order,
                    'orderLines' => $orderLines,
                    'showNokafschuining' => $orderLines->where('nokafschuining', '>', 0)->count() > 0,
                    'showVrijeRuimte' => $orderLines->where('vrije_ruimte_2', '>', 0)->count() > 0,
                    'showLb' => $orderLines->where('lb', '>', 0)->count() > 0,
                    'showCb' => $orderLines->where('fillCb', '>', 0)->count() > 0,
                    'company' => $order->company,
                    'kerndikte' => $order->kerndikte,
                ];
                break;
        }

        $pdf = Pdf::loadView($view, $data);
        return $pdf->output();
    }

    /**
     * Pakketlogica voor pakketlijst
     */
    private static function generatePakketten(Order $order): array
    {
        $dikte = intval(str_replace('mm', '', $order->kerndikte)) + 30;
        $orderlines = $order->orderLines->toArray();
        $maxDikte = 1220;

        if (empty($orderlines)) {
            return [];
        }

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
                $cb = $l['fillCb'] ?? 0;
                for ($i = 0; $i < $l['aantal']; $i++) {
                    $items[] = ['id' => $l['id'], 'dikte' => $dikte, 'lengte' => $lengte, 'cb' => $cb];
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

        $current = [];
        $currentDikte = 0;
        usort($leftovers, fn($a, $b) => $b['lengte'] <=> $a['lengte']);
        foreach ($leftovers as $item) {
            if ($currentDikte + $item['dikte'] > $maxDikte) {
                $pakketten[] = $current;
                $current = [];
                $currentDikte = 0;
            }
            $current[] = $item;
            $currentDikte += $item['dikte'];
        }
        if (!empty($current)) {
            $pakketten[] = $current;
        }

        return $pakketten;
    }
}
