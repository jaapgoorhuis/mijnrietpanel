<?php

namespace App\Services;

use Spatie\Browsershot\Browsershot;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class PanelRenderService
{
    /**
     * Genereert een PNG afbeelding van een paneel render
     */
    public function generateImage(array $panelData, int $orderId, int $panelIndex): ?string
    {
        try {
            $html = view('components.rietpanel-render', [
                'index'           => $panelIndex,
                'selectedOptions' => $panelData['selectedOptions'] ?? [],
                'panelValues'     => $panelData['panelValues'] ?? [],
                'totaleLengte'    => $panelData['totaleLengte'] ?? 0,
            ])->render();

            // Forceer absolute URL's voor alle afbeeldingen
            $baseUrl = config('app.url');

            $html = preg_replace(
                '/src="([^"]*)"/i',
                'src="' . $baseUrl . '/storage/images/rietpanel/render/$1"',
                $html
            );

            $html = str_replace(
                'asset(',
                '"' . $baseUrl . '/storage/images/rietpanel/render/',
                $html
            );

            $filename = "panel-renders/order_{$orderId}_panel_{$panelIndex}_" . time() . ".png";
            $disk = Storage::disk('public');
            $disk->makeDirectory('panel-renders');

            $fullPath = storage_path("app/public/{$filename}");

            Browsershot::html($html)
                ->windowSize(1000, 600)
                ->deviceScaleFactor(2)
                ->fullPage()
                ->waitUntilNetworkIdle()
                ->setScreenshotType('png', 100)
                ->save($fullPath);

            return $disk->url($filename);

        } catch (\Exception $e) {
            Log::error("Panel image generation failed: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Genereert afbeeldingen voor alle panelen (orderlines) in een order
     */
    public function generateAllForOrder($order): void
    {
        if (empty($order->orderLines)) {
            return;
        }

        foreach ($order->orderLines as $index => $line) {
            $data = [
                'selectedOptions' => $this->getSelectedOptions($line),
                'panelValues'     => $this->getPanelValues($line),
                'totaleLengte'    => $line->fillTotaleLengte ?? 0,
            ];

            // Dispatch de job (gaat naar achtergrond)
            \App\Jobs\GeneratePanelImageJob::dispatch($order->id, $index, $data);
        }
    }

    /**
     * Helper: bepaalt welke opties actief zijn
     */
    private function getSelectedOptions($line): array
    {
        $options = [];

        if (($line->lb ?? 0) > 0) $options[] = 1;
        if (($line->fillCb ?? 0) > 0) $options[] = 2;
        if (($line->nokafschuining ?? 0) > 0) $options[] = 3;
        if (($line->vrije_ruimte_2 ?? 0) > 0) $options[] = 4;

        return $options;
    }

    /**
     * Helper: bouwt panelValues array op uit de orderline
     */
    private function getPanelValues($line): array
    {
        $values = [];

        if (($line->lb ?? 0) > 0) $values[1] = $line->lb;
        if (($line->fillCb ?? 0) > 0) $values[2] = $line->fillCb;
        if (($line->nokafschuining ?? 0) > 0) $values[3] = $line->nokafschuining;

        if (($line->vrije_ruimte_2 ?? 0) > 0) {
            $values['4_1'] = $line->vrije_ruimte_1 ?? 0;
            $values['4_2'] = $line->vrije_ruimte_2;
        }

        // Waterstops
        $waterstops = [];
        foreach ($line->waterstops ?? [] as $ws) {
            $waterstops[] = [
                'type'      => (int) $ws->type,
                'vertical'  => (int) $ws->vertical,
                'horizontal' => (int) ($ws->horizontal ?? 0),
            ];
        }
        $values['waterstops'] = $waterstops;

        return $values;
    }
}
