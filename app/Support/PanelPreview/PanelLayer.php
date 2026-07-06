<?php

namespace App\Support\PanelPreview;

class PanelLayer
{
    public function __construct(
        public string $image,
        public ?string $clipPath = null,
        public int $zIndex = 10,
    ) {}
}
