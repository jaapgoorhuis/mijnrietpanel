<?php

namespace App\Livewire\Concerns;

use App\Services\PanelOptionValidationService;

trait HasPanelOptionValidation
{
    public function updated($property, $value): void
    {
        if (! $this->shouldValidatePanelOptions($property)) {
            return;
        }

        $this->syncTotaleLengteFromFill();

        if (str_starts_with($property, 'panelValues.')) {

            $parts = explode('.', $property);
            $index = isset($parts[1]) ? (int) $parts[1] : null;
            $field = $parts[2] ?? null;

            if ($index === null) {
                return;
            }

            // Vrije ruimte
            if (in_array($field, ['4_1', '4_2'], true)) {
                $this->validateVrijeRuimte($index);
                $this->limitVrijeRuimteStart($index);
            }

            // Waterstops
            if ($field === 'waterstops') {
                $this->validateWaterstops($index);
            }

            // Altijd alle paneelopties opnieuw valideren
            $this->validatePanelOptionsLive($index);
        }

        // Paneellengte gewijzigd
        if (str_starts_with($property, 'fillTotaleLengte.') || str_starts_with($property, 'totaleLengte.')) {

            $parts = explode('.', $property);
            $index = isset($parts[1]) ? (int) $parts[1] : null;

            if ($index !== null) {
                $this->validatePanelLengthLive($index);
                $this->validatePanelOptionsLive($index);
            }
        }
    }

    public function validateFreeSpace(int $index): void
    {
        $this->syncTotaleLengteFromFill();

        $this->limitVrijeRuimteStart($index);
        $this->validateVrijeRuimte($index);
        $this->validateWaterstops($index);           // ← nieuw
        $this->validatePanelOptionsLive($index);

        $this->normalizePanelOptions($index);
    }
    public function updatedFillTotaleLengte($value, $index): void
    {
        $this->totaleLengte[$index] = $value ?: '';

        if (method_exists($this, 'updateM2')) {
            $this->updateM2((int) $index);
        }

        $this->validatePanelLengthLive((int) $index);
        $this->validatePanelOptionsLive((int) $index);
    }

    public function validateWaterstopInput(int $index, int $wsIndex): void
    {
        $this->syncTotaleLengteFromFill();
        $this->validateWaterstops($index);
        $this->validatePanelOptionsLive($index);
        $this->normalizePanelOptions($index);
    }

    protected function validateWaterstops(int $index): void
    {
        $length = (int) ($this->panelValidationLengths()[$index] ?? 0);
        if ($length <= 0) return;

        $waterstops = $this->panelValues[$index]['waterstops'] ?? [];
        $vrijeStart = (int) ($this->panelValues[$index]['4_1'] ?? 0);
        $vrijeBreedte = (int) ($this->panelValues[$index]['4_2'] ?? 0);
        $vrijeEind = $vrijeStart + $vrijeBreedte;

        $this->resetErrorBagForWaterstops($index);

        foreach ($waterstops as $wsIndex => $ws) {
            $type = (int) ($ws['type'] ?? 0);
            $pos = (int) ($ws['vertical'] ?? 0);
            $horizontal = (int) ($ws['horizontal'] ?? 0);

            // Type nog niet gekozen → nog niet valideren
            if ($type === 0 || !in_array($type, [960, 840, 730, 500, 300], true)) {
                continue;
            }

            // 1. Verticale positie buiten paneel
            if ($pos <= 0 || $pos > $length) {
                $this->addError(
                    "panelValues.$index.waterstops.$wsIndex.vertical",
                    __('messages.Waterstop positie moet tussen 0 en de elementlengte liggen')
                );
            }

            // 2. Overlap met vrije ruimte
            if ($vrijeBreedte > 0 && $pos >= $vrijeStart && $pos <= $vrijeEind) {
                $this->addError(
                    "panelValues.$index.waterstops.$wsIndex.vertical",
                    __('messages.Waterstop mag niet in de vrije ruimte vallen')
                );
            }

            // 3. Horizontale verplaatsing ongeldig
            $maxHorizontal = app(\App\Services\PanelOptionValidationService::class)
                ->waterstopHorizontalMax($type);

            if ($horizontal < -$maxHorizontal || $horizontal > $maxHorizontal) {
                $this->addError(
                    "panelValues.$index.waterstops.$wsIndex.horizontal",
                    __('messages.horizontal_shift_max', ['max' => $maxHorizontal])
                );
            }

            // 4. Overlap tussen waterstops zelf
            foreach ($waterstops as $otherIndex => $other) {
                if ($otherIndex === $wsIndex) continue;

                $otherPos = (int) ($other['vertical'] ?? 0);
                if (abs($pos - $otherPos) < 1) {
                    $this->addError(
                        "panelValues.$index.waterstops.$wsIndex.vertical",
                        __('messages.Waterstops mogen niet overlappen')
                    );
                    break;
                }
            }
        }
    }

    protected function resetErrorBagForWaterstops(int $index): void
    {
        $waterstops = $this->panelValues[$index]['waterstops'] ?? [];
        foreach (array_keys($waterstops) as $wsIndex) {
            $this->resetErrorBag("panelValues.$index.waterstops.$wsIndex.vertical");
            $this->resetErrorBag("panelValues.$index.waterstops.$wsIndex.horizontal");
        }
    }

    protected function validateVrijeRuimte(int $index): void
    {
        $length = (int) ($this->panelValidationLengths()[$index] ?? 0);
        if ($length <= 0) {
            return;
        }

        $vrijeRuimteStart = (int) ($this->panelValues[$index]['4_1'] ?? 0);
        $vrijeRuimteBreedte = (int) ($this->panelValues[$index]['4_2'] ?? 0);
        $eindPositie = $vrijeRuimteStart + $vrijeRuimteBreedte;


        $this->resetErrorBag("panelValues.$index.4_1");
        $this->resetErrorBag("panelValues.$index.4_2");

        if ($eindPositie > $length && $length > 0) {
            $this->addError(
                "panelValues.$index.4_1",
                __('messages.De ruimte mag niet groter zijn dan het element + vrije ruimte bij elkaar opgeteld')
            );

            $this->addError(
                "panelValues.$index.4_2",
                __('messages.De vrije ruimte valt buiten het element (start + breedte mag niet groter zijn dan de elementlengte)')
            );

            // === NIEUWE CLAMPING: voorkom dat het veld te groot wordt tijdens typen ===
            $maxStart = $length - $vrijeRuimteBreedte - 1; // minimaal 1mm speling
            if ($maxStart < 0) $maxStart = 0;

            if ($vrijeRuimteStart > $maxStart) {
                $this->panelValues[$index]['4_1'] = $maxStart;
            }
        }
    }

    protected function limitVrijeRuimteStart(int $index): void
    {
        $length = (int) ($this->panelValidationLengths()[$index] ?? 0);
        if (!$length) {
            return;
        }

        $value = $this->panelValues[$index]['4_1'] ?? null;
        if ($value === '' || $value === null) {
            return;
        }

        $max = $length - 300 - 50;

        if ((int)$value > $max) {
            $this->addError(
                "panelValues.$index.4_1",
                __('messages.De positie van de vrije ruimte is te laag voor dit element')
            );

            // Alleen corrigeren bij blur, niet tijdens typen
            // Voor nu even uit laten staan:
            // $this->panelValues[$index]['4_1'] = $max;
        }
    }

    public function normalizePanelOptions(?int $index = null): void
    {
        $this->syncTotaleLengteFromFill();

        $service = app(PanelOptionValidationService::class);
        $lengths = $this->panelValidationLengths();

        $result = $service->normalize(
            $this->selectedPanelOption ?? [],
            $this->panelValues ?? [],
            $lengths,
            $index
        );

        $this->selectedPanelOption = $result['selectedPanelOption'];
        $this->panelValues = $result['panelValues'];

        $this->validatePanelOptionsLive($index);


    }

    public function validatePanelOptions(): bool
    {
        $this->syncTotaleLengteFromFill();
        $this->clearPanelValidationErrors();

        $service = app(PanelOptionValidationService::class);

        $errors = $service->validate(
            $this->selectedPanelOption ?? [],
            $this->panelValues ?? [],
            $this->panelValidationLengths()
        );

        foreach ($errors as $field => $messages) {
            foreach ($messages as $message) {
                $this->addError($field, $message);
            }
        }

        return count($errors) === 0;
    }

    public function validatePanelOptionsLive(?int $index = null): bool
    {
        $this->syncTotaleLengteFromFill();
        $this->clearPanelValidationErrors($index);

        $service = app(PanelOptionValidationService::class);

        $selected = $this->selectedPanelOption ?? [];
        $values = $this->panelValues ?? [];
        $lengths = $this->panelValidationLengths();

        if ($index !== null) {
            $selected = [$index => $selected[$index] ?? []];
            $values = [$index => $values[$index] ?? []];
            $lengths = [$index => $lengths[$index] ?? 0];
        }

        $errors = $service->validate($selected, $values, $lengths, false);

        foreach ($errors as $field => $messages) {
            foreach ($messages as $message) {
                $this->addError($field, $message);
            }
        }

        // Nieuwe validatie toevoegen
        if ($index !== null) {
            $this->validateVrijeRuimte($index);
            $this->validateWaterstops($index);           // ← nieuw
        } else {
            foreach (array_keys($lengths) as $idx) {
                $this->validateVrijeRuimte((int) $idx);
                $this->validateWaterstops((int) $idx);
            }
        }

        return count($errors) === 0 && !$this->getErrorBag()->count(); // pas aan indien nodig
    }

    public function validatePanelLengthLive(int $index): void
    {
        $this->resetErrorBag("totaleLengte.$index");
        $this->resetValidation("totaleLengte.$index");

        $value = ($this->fillTotaleLengte[$index] ?? null) ?: ($this->totaleLengte[$index] ?? null);

        if ($value === '' || $value === null) {
            return;
        }

        if (! app(PanelOptionValidationService::class)->hasValidPanelLength((int) $value)) {
            $this->addError(
                "totaleLengte.$index",
                __('messages.De lengte moet minimaal 500mm en maximaal 14500mm zijn')
            );
        }
    }

    public function hasValidPanelLength(int $index): bool
    {
        $length = (int) (($this->fillTotaleLengte[$index] ?? null) ?: ($this->totaleLengte[$index] ?? 0));

        return app(PanelOptionValidationService::class)->hasValidPanelLength($length);
    }

    public function requirePanelLength(int $index): void
    {
        if ($this->hasValidPanelLength($index)) {
            $this->resetErrorBag("totaleLengte.$index");
            return;
        }

        $this->addError(
            "totaleLengte.$index",
            __('messages.Vul eerst de elementmaat in')
        );
    }

    public function togglePanelOption(int $index, int $option): void
    {
        if (!$this->hasValidPanelLength($index)) {
            $this->addError(
                "totaleLengte.$index",
                __('messages.Vul eerst de elementmaat in')
            );

            return;
        }

        $this->resetErrorBag("totaleLengte.$index");

        $selected = $this->selectedPanelOption[$index] ?? [];

        if (in_array($option, $selected)) {
            $this->selectedPanelOption[$index] = array_values(array_diff($selected, [$option]));
        } else {
            $selected[] = $option;
            $this->selectedPanelOption[$index] = array_values(array_unique($selected));
        }

        $this->normalizePanelOptions($index);
    }

    public function toggleWaterstopChecked(int $index): void
    {
        // Oude foutmelding opruimen
        $this->resetErrorBag("totaleLengte.$index");

        // Eerst controleren of de paneellengte is ingevuld
        if (!$this->hasValidPanelLength($index)) {
            $this->addError(
                "totaleLengte.$index",
                __('messages.Vul eerst de elementmaat in')
            );

            return;
        }

        // Waterstop aan/uit zetten
        $this->waterstopEnabled[$index] = !($this->waterstopEnabled[$index] ?? false);

        if ($this->waterstopEnabled[$index]) {

            if (empty($this->panelValues[$index]['waterstops'])) {
                $this->panelValues[$index]['waterstops'] = [[
                    'type' => '',
                    'vertical' => 300,
                    'horizontal' => 0,
                ]];
            }

        } else {

            $this->panelValues[$index]['waterstops'] = [];

        }

        $this->validatePanelOptionsLive($index);
    }

    public function waterstopHorizontalMax($type): int
    {
        return app(PanelOptionValidationService::class)->waterstopHorizontalMax((int) $type);
    }

    protected function syncTotaleLengteFromFill(): void
    {
        if (! property_exists($this, 'fillTotaleLengte')) {
            return;
        }

        foreach (($this->fillTotaleLengte ?? []) as $index => $value) {
            $this->totaleLengte[$index] = $value ?: '';
        }
    }

    protected function panelValidationLengths(): array
    {
        $lengths = [];

        foreach (($this->fillTotaleLengte ?? []) as $index => $value) {
            $lengths[$index] = $value;
        }

        foreach (($this->totaleLengte ?? []) as $index => $value) {
            if (! array_key_exists($index, $lengths) || $lengths[$index] === '' || $lengths[$index] === null) {
                $lengths[$index] = $value;
            }
        }

        return $lengths;
    }

    protected function shouldValidatePanelOptions(string $property): bool
    {
        return $property === 'selectedPanelOption'
            || $property === 'panelValues'
            || str_starts_with($property, 'selectedPanelOption.')
            || str_starts_with($property, 'panelValues.')
            || str_starts_with($property, 'fillTotaleLengte.')
            || str_starts_with($property, 'totaleLengte.');
    }

    protected function clearPanelValidationErrors(?int $index = null): void
    {
        $indexes = $index !== null
            ? [$index]
            : array_unique(array_merge(
                array_keys($this->panelValidationLengths()),
                array_keys($this->panelValues ?? []),
                array_keys($this->selectedPanelOption ?? [])
            ));

        foreach ($indexes as $lineIndex) {
            $this->resetErrorBag("totaleLengte.$lineIndex");
            $this->resetValidation("totaleLengte.$lineIndex");

            foreach ([1, 2, 3, '4_1', '4_2'] as $key) {
                $this->resetErrorBag("panelValues.$lineIndex.$key");
                $this->resetValidation("panelValues.$lineIndex.$key");
            }

            foreach (($this->panelValues[$lineIndex]['waterstops'] ?? []) as $wsIndex => $waterstop) {
                foreach (['type', 'vertical', 'horizontal'] as $key) {
                    $this->resetErrorBag("panelValues.$lineIndex.waterstops.$wsIndex.$key");
                    $this->resetValidation("panelValues.$lineIndex.waterstops.$wsIndex.$key");
                }
            }
        }
    }

    protected function syncWaterstopEnabled(): void
    {
        if (! property_exists($this, 'waterstopEnabled')) {
            return;
        }

        foreach (($this->panelValues ?? []) as $index => $values) {
            $this->waterstopEnabled[$index] = ! empty($values['waterstops'] ?? []);
        }
    }
}
