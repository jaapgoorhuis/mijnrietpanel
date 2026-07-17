<?php

namespace App\Jobs;

use App\Services\PanelRenderService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GeneratePanelImageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $orderId;
    public $panelIndex;
    public $panelData;

    public function __construct(int $orderId, int $panelIndex, array $panelData)
    {
        $this->orderId = $orderId;
        $this->panelIndex = $panelIndex;
        $this->panelData = $panelData;
    }

    public function handle(PanelRenderService $renderService)
    {
        $imageUrl = $renderService->generateImage(
            $this->panelData,
            $this->orderId,
            $this->panelIndex
        );

        if ($imageUrl) {
            \App\Models\OrderLines::where('order_id', $this->orderId)
                ->skip($this->panelIndex)
                ->take(1)
                ->update(['render_image' => $imageUrl]);
        }
    }
}
