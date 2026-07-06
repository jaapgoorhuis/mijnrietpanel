<?php

namespace App\Support\PanelPreview;

class Geometry
{
    public function __construct(
        public int $imageWidth = 2048,
        public int $imageHeight = 585,
        public int $panelLength = 7500,

        // gekalibreerd op jouw 2048x585 render
        public Point $topLeft = new Point(255, 105),
        public Point $topRight = new Point(1810, 105),
        public Point $bottomLeft = new Point(62, 287),
        public Point $bottomRight = new Point(1995, 287),
    ) {}

    public function ratio(float $mm): float
    {
        return max(0, min(1, $mm / $this->panelLength));
    }

    public function topPoint(float $mm): Point
    {
        $r = $this->ratio($mm);

        return new Point(
            $this->topLeft->x + (($this->topRight->x - $this->topLeft->x) * $r),
            $this->topLeft->y + (($this->topRight->y - $this->topLeft->y) * $r),
        );
    }

    public function bottomPoint(float $mm): Point
    {
        $r = $this->ratio($mm);

        return new Point(
            $this->bottomLeft->x + (($this->bottomRight->x - $this->bottomLeft->x) * $r),
            $this->bottomLeft->y + (($this->bottomRight->y - $this->bottomLeft->y) * $r),
        );
    }

    public function polygonBetween(float $fromMm, float $toMm): string
    {
        $a = $this->topPoint($fromMm);
        $b = $this->topPoint($toMm);
        $c = $this->bottomPoint($toMm);
        $d = $this->bottomPoint($fromMm);

        return "{$a->x},{$a->y} {$b->x},{$b->y} {$c->x},{$c->y} {$d->x},{$d->y}";
    }

    public function displayMm(float $mm, float $minMm = 160): float
    {
        if ($mm <= 0) {
            return 0;
        }

        return max($mm, $minMm);
    }

    public function displayPx(float $mm, float $minPx = 90): float
    {
        if ($mm <= 0) {
            return 0;
        }

        $px = ($mm / $this->panelLength) * $this->imageWidth;

        return max($px, $minPx);
    }
}
