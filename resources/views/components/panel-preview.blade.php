@props([
    'index' => 0,
    'selectedOptions' => [],
    'panelValues' => [],
    'totaleLengte' => 0,
])

@php
    $selectedOptions = array_map('intval', $selectedOptions);

    $hasLayback = in_array(1, $selectedOptions);
    $hasCutback = in_array(2, $selectedOptions);
    $hasNok = in_array(3, $selectedOptions);
    $hasVrijeRuimte = in_array(4, $selectedOptions);

    $basePath = 'storage/images/rietpanel/render/';

    $aImage = $hasNok
        ? 'A4_LB_N.png'
        : ($hasLayback ? 'A3_LB.png' : 'A1_Paneel.png');

    $bImage = 'B1_Paneel.png';
    $vrImage = 'B2_VR.png';
    $cImage = $hasCutback ? 'C2_CB.png' : 'C1_Paneel.png';

    $lengte = (int) $totaleLengte;

    if ($lengte <= 3000) {
        $totalB = 3;
    } elseif ($lengte <= 7000) {
        $totalB = 4;
    } elseif ($lengte <= 11000) {
        $totalB = 5;
    } else {
        $totalB = 6;
    }

    $ruimteVanafLinks = (int)($panelValues['4_1'] ?? 0);

    $insertSlots = $totalB + 1;
    $positie = 0;

    if ($hasVrijeRuimte && $lengte > 0) {
        $positieRatio = $ruimteVanafLinks / $lengte;
        $positieRatio = max(0, min(1, $positieRatio));

        $positie = (int) round($positieRatio * $insertSlots);
        $positie = max(0, min($insertSlots, $positie));
    }

    $parts = [];
    $parts[] = ['image' => $aImage, 'type' => 'a'];

    for ($i = 0; $i <= $totalB; $i++) {
        if ($hasVrijeRuimte && $i === $positie) {
            $parts[] = ['image' => $vrImage, 'type' => 'vr'];
        }

        if ($i < $totalB) {
            $parts[] = ['image' => $bImage, 'type' => 'b'];
        }
    }

    $parts[] = ['image' => $cImage, 'type' => 'c'];

    $weights = [
        'a' => 0.991,
        'b' => 1.00,
        'vr' => 1.00,
        'c' => 0.991,
    ];

    $totalWeight = collect($parts)->sum(fn($part) => $weights[$part['type']]);

    $cursor = 0;
    $partPositions = [];

    foreach ($parts as $key => $part) {
        $width = ($weights[$part['type']] / $totalWeight) * 100;

        $partPositions[$key] = [
            'left' => $cursor,
            'width' => $width,
            'type' => $part['type'],
        ];

        $cursor += $width;
    }

    $lbLineLeft = 4;
    $lbLineWidth = 10;

    $cbLineWidth = 10;
    $cbLineLeft = 100 - $cbLineWidth - 3;

    $vrLineLeft = 45;
    $vrLineWidth = 10;

    foreach ($partPositions as $pos) {
        if ($pos['type'] === 'vr') {
            $vrLineLeft = $pos['left'];
            $vrLineWidth = $pos['width'];
            break;
        }
    }
@endphp

<div class="relative w-full max-w-full overflow-visible pt-14">

    <div class="absolute top-0 left-0 w-full h-12 text-[10px] md:text-[11px] font-bold pointer-events-none">

        @if($totaleLengte)
            <div class="absolute left-1/2 -translate-x-1/2 top-0 whitespace-nowrap">
                {{ __('messages.Totale maat') }}: &lt; {{ $totaleLengte }} {{ __('messages.mm') }} &gt;
            </div>
        @endif

        @if($hasLayback)
            <div class="absolute top-6 text-center" style="left: {{ $lbLineLeft }}%; width: {{ $lbLineWidth }}%;">
                <div class="flex items-center w-full">
                    <span>|</span>
                    <span class="flex-1 border-t border-black"></span>
                    <span>|</span>
                </div>
                <div class="leading-none whitespace-nowrap">
                    {{ $panelValues['1'] ?? 0 }} {{ __('messages.mm') }}
                </div>
            </div>
        @endif

        @if($hasVrijeRuimte)
            <div class="absolute top-6 text-center" style="left: {{ $vrLineLeft }}%; width: {{ $vrLineWidth }}%;">
                <div class="flex items-center w-full">
                    <span>|</span>
                    <span class="flex-1 border-t border-black"></span>
                    <span>|</span>
                </div>
                <div class="leading-none whitespace-nowrap">
                    {{ $panelValues['4_2'] ?? 0 }} {{ __('messages.mm') }}
                </div>
            </div>
        @endif

        @if($hasCutback)
            <div class="absolute top-6 text-center" style="left: {{ $cbLineLeft }}%; width: {{ $cbLineWidth }}%;">
                <div class="flex items-center w-full">
                    <span>|</span>
                    <span class="flex-1 border-t border-black"></span>
                    <span>|</span>
                </div>
                <div class="leading-none whitespace-nowrap">
                    {{ $panelValues['2'] ?? 0 }} {{ __('messages.mm') }}
                </div>
            </div>
        @endif
    </div>

    <div class="w-full max-w-full overflow-hidden">
        <div class="flex w-full max-w-full items-start overflow-hidden">
            @foreach($parts as $part)
                @php
                    $width = ($weights[$part['type']] / $totalWeight) * 100;
                @endphp

                <img
                    src="{{ asset($basePath . $part['image']) }}"
                    alt="Paneel onderdeel"
                    class="block h-auto min-w-0 object-fill"
                    style="width: {{ $width }}%; flex: 0 1 {{ $width }}%;"
                >
            @endforeach
        </div>
    </div>
</div>
