<?php

namespace App\Services;

class PanelOptionValidationService
{
    public const PANEL_LENGTH_MIN = 500;
    public const PANEL_LENGTH_MAX = 14500;

    public const NOK_MIN = 1;
    public const NOK_MAX = 60;

    public const VR_TOP_MIN = 300;
    public const VR_BOTTOM_MIN = 300;
    public const VR_WATERSTOP = 200;
    public const VR_WIDTH_MIN = 50;

    public const WATERSTOP_VERTICAL_MIN = 300;
    public const WATERSTOP_BOTTOM_RESERVED = 600;
    public const WATERSTOP_LENGTH_MM = 200;

    public const WATERSTOP_TYPES = [960, 840, 730, 500, 300];

    public function normalize(
        array $selectedPanelOption,
        array $panelValues,
        array $lengths,
        ?int $lineIndex = null
    ): array {
        $indexes = $lineIndex === null
            ? array_keys($lengths + $selectedPanelOption + $panelValues)
            : [$lineIndex];

        foreach ($indexes as $index) {
            $length = (int) ($lengths[$index] ?? 0);

            $selectedPanelOption[$index] = array_values(array_unique(array_map(
                'intval',
                $selectedPanelOption[$index] ?? []
            )));

            sort($selectedPanelOption[$index]);

            $panelValues[$index] = $this->defaultPanelValues($panelValues[$index] ?? []);

            if (! $this->hasValidPanelLength($length)) {
                $selectedPanelOption[$index] = [];
                $panelValues[$index]['waterstops'] = [];
                continue;
            }

            $panelValues[$index] = $this->normalizeNok($panelValues[$index], $selectedPanelOption[$index]);
            $panelValues[$index] = $this->normalizeVrijeRuimte($panelValues[$index], $selectedPanelOption[$index], $length);
            $panelValues[$index] = $this->normalizeWaterstops($panelValues[$index], $length);
            $panelValues[$index] = $this->resolveOverlaps($panelValues[$index], $selectedPanelOption[$index], $length);
        }

        return [
            'selectedPanelOption' => $selectedPanelOption,
            'panelValues' => $panelValues,
        ];

    }

    public function validate(
        array $selectedPanelOption,
        array $panelValues,
        array $lengths,
        bool $strictRequired = true
    ): array {
        $errors = [];
        $indexes = array_keys($lengths + $selectedPanelOption + $panelValues);

        foreach ($indexes as $index) {
            $rawLength = $lengths[$index] ?? null;

            if ($rawLength === '' || $rawLength === null) {
                if ($strictRequired) {
                    $errors["totaleLengte.$index"][] = __('messages.De lengte is een verplicht veld');
                }
                continue;
            }

            $length = (int) $rawLength;

            if (! $this->hasValidPanelLength($length)) {
                $errors["totaleLengte.$index"][] = __('messages.De lengte moet minimaal 500mm en maximaal 14500mm zijn');
                continue;
            }

            $options = array_map('intval', $selectedPanelOption[$index] ?? []);
            $values = $this->defaultPanelValues($panelValues[$index] ?? []);

            if (!empty($values['_errors'])) {
                foreach ($values['_errors'] as $field => $messages) {
                    foreach ($messages as $message) {
                        $errors["panelValues.$index.$field"][] = $message;
                    }
                }
            }

            if (in_array(3, $options, true)) {
                $nok = (int) ($values[3] ?? 0);

                if ($nok < self::NOK_MIN || $nok > self::NOK_MAX) {
                    $errors["panelValues.$index.3"][] = __('messages.De nokafschuining moet tussen 1 en 60 graden zijn');
                }
            }

            if (in_array(4, $options, true)) {
                $vrStart = (int) ($values['4_1'] ?? 0);
                $vrWidth = (int) ($values['4_2'] ?? 0);

                $minTop = in_array(1, $options, true)
                    ? 500
                    : self::VR_TOP_MIN;

                if ($vrStart < $minTop) {
                    $errors["panelValues.$index.4_1"][] =
                        __('messages.Ruimte bovenkant tot vrije ruimte moet minimaal ') . $minTop . ' ' . __('messages.mm');
                }


                $maxStart = $length - self::VR_BOTTOM_MIN - self::VR_WIDTH_MIN;

                if ($vrStart > $maxStart) {
                    $errors["panelValues.$index.4_1"][] =
                        __('messages.De positie van de vrije ruimte is te laag voor dit paneel');
                }

                if ($vrWidth < self::VR_WIDTH_MIN) {
                    $errors["panelValues.$index.4_2"][] = __('messages.Vrije ruimte moet minimaal 50mm zijn');
                }

                $maxWidth = $length - $vrStart - self::VR_BOTTOM_MIN;

                if ($maxWidth < self::VR_WIDTH_MIN) {
                    $errors["panelValues.$index.4_2"][] =
                        __('messages.panelToShort');
                }

                elseif ($vrWidth > $maxWidth) {
                    $errors["panelValues.$index.4_2"][] =
                        __('messages.De vrije ruimte is te groot voor dit paneel');
                }
            }

            foreach (($values['waterstops'] ?? []) as $wsIndex => $waterstop) {

                // Nieuwe lege waterstop nog niet valideren
                if (
                    empty($waterstop['type']) &&
                    empty($waterstop['vertical'])
                ) {
                    continue;
                }

                if (($waterstop['type'] ?? '') === '') {
                    continue;
                }

                $type = (int) $waterstop['type'];
                $vertical = (int) ($waterstop['vertical'] ?? 0);
                $horizontal = (int) ($waterstop['horizontal'] ?? 0);

                if (! in_array($type, self::WATERSTOP_TYPES, true)) {
                    if ($strictRequired || ($waterstop['type'] ?? '') !== '') {
                        $errors["panelValues.$index.waterstops.$wsIndex.type"][] = __('messages.Selecteer een geldig type waterstop');
                    }
                    continue;
                }

                $maxVertical = $this->maxWaterstopVertical($length);

                if ($vertical < self::WATERSTOP_VERTICAL_MIN || $vertical > $maxVertical) {
                    $errors["panelValues.$index.waterstops.$wsIndex.vertical"][] =
                        __('messages.De verticale positie moet minimaal 300mm zijn en maximaal ') . $maxVertical . ' ' . __('messages.mm');
                }

                $maxHorizontal = $this->waterstopHorizontalMax($type);

                if ($horizontal < -$maxHorizontal || $horizontal > $maxHorizontal) {
                    $errors["panelValues.$index.waterstops.$wsIndex.horizontal"][] =
                        __('messages.De horizontale verplaatsing mag maximaal ') . $maxHorizontal . ' ' . __('messages.mm zijn');
                }
            }

            $overlapErrors = $this->validateOverlaps($values, $options, $index);
            $errors = array_merge_recursive($errors, $overlapErrors);
        }

        return $errors;
    }

    public function hasValidPanelLength(int $length): bool
    {
        return $length >= self::PANEL_LENGTH_MIN && $length <= self::PANEL_LENGTH_MAX;
    }

    public function waterstopHorizontalMax(int $type): int
    {
        return match ($type) {
            960 => 0,
            840 => 60,
            730 => 115,
            500 => 230,
            300 => 330,
            default => 0,
        };
    }

    public function maxVrijeRuimteBreedte(int $length, int $start): int
    {
        return max(
            self::VR_WIDTH_MIN,
            $length - $start - self::VR_BOTTOM_MIN
        );
    }


    public function maxWaterstopVertical(int $length): int
    {
        return max(
            self::WATERSTOP_VERTICAL_MIN,
            $length - self::WATERSTOP_BOTTOM_RESERVED
        );
    }

    private function defaultPanelValues(array $values): array
    {
        $legacyWaterstops = [];

        if (! empty($values['waterstop_type'])) {
            $legacyWaterstops[] = [
                'type' => $values['waterstop_type'],
                'vertical' => $values['waterstop_vertical'] ?? '',
                'horizontal' => $values['waterstop_horizontal'] ?? 0,
            ];
        }

        $values = array_replace([
            1 => 20,
            2 => 20,
            3 => 1,
            '4_1' => 300,
            '4_2' => 50,
            'waterstops' => $legacyWaterstops,
        ], $values);

        if (! is_array($values['waterstops'] ?? null)) {
            $values['waterstops'] = [];
        }

        return $values;
    }

    private function normalizeNok(array $values, array $options): array
    {
        if (! in_array(3, $options, true)) {
            return $values;
        }

        // Leeg laten tijdens typen
        if (($values[3] ?? '') === '') {
            return $values;
        }

        // Niet begrenzen
        $values[3] = (int) $values[3];

        return $values;
    }

    private function normalizeVrijeRuimte(array $values, array $options, int $length): array
    {
        if (!in_array(4, $options, true)) {
            return $values;
        }

        $minTop = in_array(1, $options, true)
            ? 500
            : self::VR_TOP_MIN;


        // tijdens typen leeg laten
        if (($values['4_1'] ?? null) === '') {
            return $values;
        }

        if (($values['4_2'] ?? null) === '') {
            return $values;
        }


        $start = (int) $values['4_1'];
        $width = (int) $values['4_2'];


        /*
         * Maximale positie:
         * er moet altijd minimaal 300mm onderruimte
         * én minimaal 50mm vrije ruimte overblijven
         */
        $maxStart = $length - self::VR_BOTTOM_MIN - self::VR_WIDTH_MIN;


        // 4_1 begrenzen
        $values['4_1'] = max(
            $minTop,
            min(
                $start,
                $maxStart
            )
        );


        // 4_2 begrenzen op basis van nieuwe positie
        $maxWidth = $this->maxVrijeRuimteBreedte(
            $length,
            (int) $values['4_1']
        );


        $values['4_2'] = max(
            self::VR_WIDTH_MIN,
            min(
                $width,
                $maxWidth
            )
        );


        return $values;
    }
    private function normalizeWaterstops(array $values, int $length): array
    {
        $waterstops = $values['waterstops'] ?? [];

        if (empty($waterstops) || ! is_array($waterstops)) {
            $values['waterstops'] = [];
            return $values;
        }

        $maxVertical = $this->maxWaterstopVertical($length);

        foreach ($waterstops as $index => $waterstop) {

            $type = $waterstop['type'] ?? '';

            // Type nog niet gekozen
            if ($type === '' || !in_array((int)$type, self::WATERSTOP_TYPES, true)) {
                $values['waterstops'][$index] = [
                    'type' => $type,
                    'vertical' => $waterstop['vertical'] ?? '',
                    'horizontal' => $waterstop['horizontal'] ?? 0,
                ];

                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | Verticale positie
            | Lege input nooit automatisch vullen.
            | Anders springt Livewire terug naar 300 tijdens typen.
            |--------------------------------------------------------------------------
            */
            $verticalInput = $waterstop['vertical'] ?? '';

            if ($verticalInput === '') {
                $vertical = '';
            } else {
                $vertical = (int) $verticalInput;

                $vertical = max(
                    self::WATERSTOP_VERTICAL_MIN,
                    min($maxVertical, $vertical)
                );
            }


            /*
            |--------------------------------------------------------------------------
            | Horizontale positie
            |--------------------------------------------------------------------------
            */
            $horizontalInput = $waterstop['horizontal'] ?? 0;

            if ($horizontalInput === '') {
                $horizontal = '';
            } else {
                $horizontal = (int) $horizontalInput;

                $maxHorizontal = $this->waterstopHorizontalMax((int)$type);

                $horizontal = max(
                    -$maxHorizontal,
                    min($maxHorizontal, $horizontal)
                );
            }


            $values['waterstops'][$index] = [
                'type' => (int)$type,
                'vertical' => $vertical,
                'horizontal' => $horizontal,
            ];
        }


        $values['waterstops'] = array_values($values['waterstops']);

        return $values;
    }

    private function resolveOverlaps(array $values, array $options, int $length): array
    {
        if (empty($values['waterstops'])) {
            return $values;
        }

        $blockedRanges = [];

        if (in_array(4, $options, true)) {
            $vrStart = (int) ($values['4_1'] ?? 0);
            $vrWidth = (int) ($values['4_2'] ?? 0);

            if ($vrStart && $vrWidth) {
                $blockedRanges[] = [
                    'start' => $vrStart,
                    'end' => $vrStart + $vrWidth + self::VR_WATERSTOP,
                ];
            }
        }

        $maxVertical = $this->maxWaterstopVertical($length);
        $placedRanges = [];

        foreach ($values['waterstops'] as $index => $waterstop) {
            $type = (int) ($waterstop['type'] ?? 0);

            if (! in_array($type, self::WATERSTOP_TYPES, true)) {
                continue;
            }

            $start = (int) ($waterstop['vertical'] ?? self::WATERSTOP_VERTICAL_MIN);
            $start = $this->findAvailableStart(
                $start,
                self::WATERSTOP_LENGTH_MM,
                array_merge($blockedRanges, $placedRanges),
                self::WATERSTOP_VERTICAL_MIN,
                $maxVertical
            );

            $values['waterstops'][$index]['vertical'] = $start;
            $placedRanges[] = ['start' => $start, 'end' => $start + self::WATERSTOP_LENGTH_MM];
        }

        return $values;
    }

    private function validateOverlaps(array $values, array $options, int $lineIndex): array
    {
        $errors = [];
        $ranges = [];

        if (in_array(4, $options, true)) {
            $vrStart = (int) ($values['4_1'] ?? 0);
            $vrWidth = (int) ($values['4_2'] ?? 0);

            if ($vrStart && $vrWidth) {
                $ranges[] = [
                    'type' => 'vrije_ruimte',
                    'start' => $vrStart,
                    'end' => $vrStart + $vrWidth + self::VR_WATERSTOP,
                    'field' => "panelValues.$lineIndex.4_1",
                ];
            }
        }

        foreach (($values['waterstops'] ?? []) as $wsIndex => $waterstop) {
            $type = (int) ($waterstop['type'] ?? 0);

            if (! in_array($type, self::WATERSTOP_TYPES, true)) {
                continue;
            }

            $start = (int) ($waterstop['vertical'] ?? 0);
            $ranges[] = [
                'type' => 'waterstop',
                'start' => $start,
                'end' => $start + self::WATERSTOP_LENGTH_MM,
                'field' => "panelValues.$lineIndex.waterstops.$wsIndex.vertical",
            ];
        }

        for ($i = 0; $i < count($ranges); $i++) {
            for ($j = $i + 1; $j < count($ranges); $j++) {
                if (! $this->rangesOverlap($ranges[$i]['start'], $ranges[$i]['end'], $ranges[$j]['start'], $ranges[$j]['end'])) {
                    continue;
                }

                $message = __('messages.Waterstop en vrije ruimte mogen elkaar niet overlappen');

                if ($ranges[$i]['type'] === 'waterstop' && $ranges[$j]['type'] === 'waterstop') {
                    $message = __('messages.Waterstops mogen elkaar niet overlappen');
                }

                $errors[$ranges[$i]['field']][] = $message;
                $errors[$ranges[$j]['field']][] = $message;
            }
        }

        return $errors;
    }

    private function findAvailableStart(int $preferredStart, int $size, array $ranges, int $min, int $max): int
    {
        $start = max($min, min($max, $preferredStart));
        usort($ranges, fn ($a, $b) => $a['start'] <=> $b['start']);

        foreach ($ranges as $range) {
            if ($this->rangesOverlap($start, $start + $size, $range['start'], $range['end'])) {
                $start = (int) $range['end'];
                $start = max($min, min($max, $start));
            }
        }

        if (! $this->overlapsAny($start, $start + $size, $ranges)) {
            return $start;
        }

        for ($candidate = $min; $candidate <= $max; $candidate += 10) {
            if (! $this->overlapsAny($candidate, $candidate + $size, $ranges)) {
                return $candidate;
            }
        }

        return max($min, min($max, $preferredStart));
    }

    private function overlapsAny(int $start, int $end, array $ranges): bool
    {
        foreach ($ranges as $range) {
            if ($this->rangesOverlap($start, $end, $range['start'], $range['end'])) {
                return true;
            }
        }

        return false;
    }

    private function rangesOverlap(int $aStart, int $aEnd, int $bStart, int $bEnd): bool
    {
        return $aStart < $bEnd && $bStart < $aEnd;
    }
}
