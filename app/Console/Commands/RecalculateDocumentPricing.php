<?php

namespace App\Console\Commands;

use App\Models\Offerte;
use App\Models\Order;
use App\Services\PricingServices;
use Illuminate\Console\Command;

class RecalculateDocumentPricing extends Command
{
    protected $signature = 'pricing:recalculate {type=all}';
    protected $description = 'Recalculate stored pricing for existing offertes and orders';

    public function handle(PricingServices $pricingServices): int
    {
        $type = $this->argument('type');

        if ($type === 'all' || $type === 'offertes') {
            $this->info('Offertes bijwerken...');

            Offerte::with(['user', 'offerteLines'])->chunkById(100, function ($offertes) use ($pricingServices) {
                foreach ($offertes as $offerte) {
                    if (!$offerte->user) {
                        $this->warn('Offerte overgeslagen, geen user gevonden: ' . $offerte->id);
                        continue;
                    }

                    if (!$offerte->user->bedrijf_id) {
                        $this->warn('Offerte overgeslagen, geen bedrijf_id gevonden: ' . $offerte->id);
                        continue;
                    }

                    $pricingServices->updateDocumentPricing($offerte);

                    $this->line('Offerte bijgewerkt: ' . $offerte->id);
                }
            });
        }

        if ($type === 'all' || $type === 'orders') {
            $this->info('Orders bijwerken...');

            Order::with(['user', 'orderLines'])->chunkById(100, function ($orders) use ($pricingServices) {
                foreach ($orders as $order) {
                    if (!$order->user) {
                        $this->warn('Order overgeslagen, geen user gevonden: ' . $order->id);
                        continue;
                    }

                    if (!$order->user->bedrijf_id) {
                        $this->warn('Order overgeslagen, geen bedrijf_id gevonden: ' . $order->id);
                        continue;
                    }

                    $pricingServices->updateDocumentPricing($order);

                    $this->line('Order bijgewerkt: ' . $order->id);
                }
            });
        }

        $this->info('Klaar.');

        return self::SUCCESS;
    }
}
