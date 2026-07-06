<?php

namespace App\Support\PanelPreview;

class PanelRenderer
{
    public Geometry $geometry;

    public function __construct(
        public int $length,
        public array $options = [],
        public array $values = [],
    ) {
        $this->length = $this->length > 0 ? $this->length : 7500;

        $this->geometry = new Geometry(
            panelLength: $this->length,
        );
    }

    public function has(int $option): bool
    {
        return in_array($option, $this->options);
    }

    public function value(string|int $key, int $default = 0): int
    {
        return (int)($this->values[$key] ?? $default);
    }

    public function baseImage(): string
    {
        return asset('storage/images/rietpanel/preview/Paneel.png');
    }

    public function layers(): array
    {
        $layers = [];

        if ($this->has(1) && $this->value(1) > 0) {
            $lb = $this->geometry->displayPx($this->value(1));

            $layers[] = new PanelLayer(
                image: asset('storage/images/rietpanel/preview/Paneel-LB.png'),
                clipPath: "rect:0,0,{$lb},585",
                zIndex: 20,
            );
        }

// Cutback
        if ($this->has(2) && $this->value(2) > 0) {
            $cb = $this->geometry->displayPx($this->value(2));
            $x = $this->geometry->imageWidth - $cb;

            $layers[] = new PanelLayer(
                image: asset('storage/images/rietpanel/preview/Paneel-CB.png'),
                clipPath: "rect:{$x},0,{$cb},585",
                zIndex: 30,
            );
        }

        if ($this->has(3) && $this->value(3) > 0) {
            $layers[] = new PanelLayer(
                image: asset('storage/images/rietpanel/preview/Paneel-N.png'),
                clipPath: $this->geometry->polygonBetween(0, 450),
                zIndex: 40,
            );
        }

        if ($this->has(4) && $this->value('4_2') > 0) {
            $from = $this->value('4_1');
            $to = $from + $this->value('4_2');

            $layers[] = new PanelLayer(
                image: asset('storage/images/rietpanel/preview/Paneel-VR.png'),
                clipPath: $this->geometry->polygonBetween($from, $to),
                zIndex: 25,
            );
        }

        return $layers;
    }
}
