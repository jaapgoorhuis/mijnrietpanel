<?php

namespace App\Services;

use App\Models\PriceRules;
use App\Models\PanelType;
use App\Models\Surcharges;
use App\Models\Company;
use App\Models\Offerte;
use App\Models\Order;
use App\Models\OfferteSurcharge;
use App\Models\OrderSurcharge;

class PricingServices
{
    public function calculate($document, $lines = null, $company = null): array
    {
        $company = $company ?: Company::find($document->user->bedrijf_id);
        $lines = $lines ?: $this->getLines($document);

        $panelType = PanelType::where('name', $document->kerndikte)->first();

        $subtotal = 0;
        $lineTotals = [];

        $zaaglengtes = 0;
        $laybacks = 0;
        $nokafschuining = 0;
        $vrijeruimte = 0;

        $zaaglengteToeslag = Surcharges::where('rule', 'zaaglengte')->first();

        foreach ($lines as $line) {
            $m2price = $this->calculateM2Price($document, $company, $panelType);
            $lineTotal = (float) $line->m2 * (float) $m2price;

            $lineTotals[$line->id] = $lineTotal;
            $subtotal += $lineTotal;

            if ((float) $line->lb > 0) {
                $laybacks += (int) $line->aantal;
            }

            if ((float) $line->nokafschuining > 0) {
                $nokafschuining += (int) $line->aantal;
            }

            if ((float) $line->vrije_ruimte_2 > 0) {
                $vrijeruimte += (int) $line->aantal;
            }

            if ($zaaglengteToeslag && (float) $line->fillTotaleLengte < (float) $zaaglengteToeslag->number) {
                $zaaglengtes += (int) $line->aantal;
            }
        }

        $totalM2 = $lines->sum('m2');

        $orderLineHeeftOversize = false;
        $oversizeThreshold = Surcharges::where('rule', 'order')->value('number');

        foreach ($lines as $line) {
            if ($oversizeThreshold && (float) $line->fillTotaleLengte > (float) $oversizeThreshold) {
                $orderLineHeeftOversize = true;
                break;
            }
        }

        $surchargeRows = [];
        $surchargesTotal = 0;

        foreach (Surcharges::all() as $surcharge) {
            $amount = 0;
            $qty = 0;

            if ($surcharge->rule === 'zaaglengte' && $zaaglengtes > 0) {
                $qty = $zaaglengtes;
                $amount = $qty * (float) $surcharge->price;
            }

            if ($surcharge->rule === 'Layback' && $laybacks > 0) {
                $qty = $laybacks;
                $amount = $qty * (float) $surcharge->price;
            }

            if ($surcharge->rule === 'Nokafschuining' && $nokafschuining > 0) {
                $qty = $nokafschuining;
                $amount = $qty * (float) $surcharge->price;
            }

            if ($surcharge->rule === 'Vrije ruimte' && $vrijeruimte > 0) {
                $qty = $vrijeruimte;
                $amount = $qty * (float) $surcharge->price;
            }

            if ($surcharge->rule === 'vierkantemeter' && (float) $totalM2 < (float) $surcharge->number) {
                $qty = 1;
                $amount = (float) $surcharge->price;
            }

            if ($surcharge->rule === 'order' && $orderLineHeeftOversize) {
                $qty = 1;
                $amount = (float) $surcharge->price;
            }

            if ($amount > 0) {
                $surchargeRows[] = [
                    'name' => $surcharge->name,
                    'rule' => $surcharge->rule,
                    'qty' => $qty,
                    'unit_price' => (float) $surcharge->price,
                    'total' => $amount,
                ];

                $surchargesTotal += $amount;
            }
        }

        $rulePrice = $this->getRulePrice($document);
        $vat = ($subtotal + $surchargesTotal) * 0.21;
        $grandTotal = $subtotal + $surchargesTotal + $vat + $rulePrice;

        return [
            'line_totals' => $lineTotals,
            'surcharge_rows' => $surchargeRows,
            'subtotal' => $subtotal,
            'surcharges_total' => $surchargesTotal,
            'total_m2' => $totalM2,
            'vat' => $vat,
            'rule_price' => $rulePrice,
            'grand_total' => $grandTotal,
            'has_surcharges' => count($surchargeRows) > 0,

            'zaaglengtes' => $zaaglengtes,
            'laybacks' => $laybacks,
            'nokafschuining' => $nokafschuining,
            'vrijeruimte' => $vrijeruimte,
            'order_line_heeft_oversize' => $orderLineHeeftOversize,
        ];
    }

    public function updateDocumentPricing($document): void
    {
        $document->loadMissing(['user']);

        $company = Company::find($document->user->bedrijf_id);
        $lines = $this->getLines($document);
        $panelType = PanelType::where('name', $document->kerndikte)->first();

        foreach ($lines as $line) {
            $line->price_per_m2 = $this->calculateM2Price($document, $company, $panelType);
            $line->save();
        }

        $pricing = $this->calculate($document, $lines, $company);

        $this->storeSurcharges($document, $pricing['surcharge_rows']);

        $document->subtotal = $pricing['subtotal'];
        $document->surcharges_total = $pricing['surcharges_total'];
        $document->vat_total = $pricing['vat'];
        $document->grand_total = $pricing['grand_total'];
        $document->prices_updated_at = now();
        $document->save();
    }

    private function storeSurcharges($document, array $surchargeRows): void
    {
        if ($document instanceof Offerte) {
            OfferteSurcharge::where('offerte_id', $document->id)->delete();

            foreach ($surchargeRows as $row) {
                OfferteSurcharge::create([
                    'offerte_id' => $document->id,
                    'name' => $row['name'] ?? null,
                    'rule' => $row['rule'] ?? null,
                    'qty' => $row['qty'] ?? 1,
                    'unit_price' => $row['unit_price'] ?? 0,
                    'total' => $row['total'] ?? 0,
                ]);
            }

            return;
        }

        if ($document instanceof Order) {
            OrderSurcharge::where('order_id', $document->id)->delete();

            foreach ($surchargeRows as $row) {
                OrderSurcharge::create([
                    'order_id' => $document->id,
                    'name' => $row['name'] ?? null,
                    'rule' => $row['rule'] ?? null,
                    'qty' => $row['qty'] ?? 1,
                    'unit_price' => $row['unit_price'] ?? 0,
                    'total' => $row['total'] ?? 0,
                ]);
            }
        }
    }

    private function getLines($document)
    {
        if ($document instanceof Offerte) {
            return $document->offerteLines;
        }

        if ($document instanceof Order) {
            return $document->orderLines;
        }

        return collect();
    }

    private function getRulePrice($document): float
    {
        if (
            $document instanceof Offerte &&
            method_exists($document, 'offerteRules') &&
            $document->offerteRules
        ) {
            return (float) $document->offerteRules->price;
        }

        if (
            $document instanceof Order &&
            method_exists($document, 'orderRules') &&
            $document->orderRules
        ) {
            return (float) $document->orderRules->price;
        }

        return 0;
    }

    private function calculateM2Price($document, $company, $panelType): float
    {
        if (!$company || !$panelType) {
            return 0;
        }

        if ($company->is_reseller) {
            $priceRule = PriceRules::where('panel_type', $panelType->id)
                ->where('company_id', $company->id)
                ->first();

            $discount = 0;
        } else {
            $priceRule = PriceRules::where('panel_type', $panelType->id)->first();

            $discount = $priceRule
                ? (float) $priceRule->price / 100 * (float) $company->discount
                : 0;
        }

        if (!$priceRule) {
            return 0;
        }

        $base = (float) $priceRule->price - (float) $discount;
        $marge = $base / 100 * (float) ($document->marge ?? 0);
        $disc = $base / 100 * (float) ($document->discount ?? 0);

        return $company->is_reseller
            ? $base - $disc
            : (($document->marge ?? 0) != 0 ? $base + $marge : $base);
    }
}
