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
    $panelHeightPx = 220;
    $visualContainerWidth = 600;

    $length = max(1, (int) $totaleLengte);
    $mmToPx = $visualContainerWidth / max($length, 1000);

    $laybackMm = $hasLayback ? (int) ($panelValues['1'] ?? 0) : 0;
    $cutbackMm = $hasCutback ? max(20, min(200, (int) ($panelValues['2'] ?? 20))) : 0;
    $freeSpaceFromLeft = (int) ($panelValues['4_1'] ?? ($hasLayback ? 500 : 300));
    $freeSpaceSize = (int) ($panelValues['4_2'] ?? 0);

    if ($hasVrijeRuimte) {
        $freeSpaceFromLeft = max(0, min($length, $freeSpaceFromLeft));
        $maxFreeSpace = max(0, $length - $freeSpaceFromLeft - 300);
        $freeSpaceSize = max(0, min($freeSpaceSize, $maxFreeSpace));
    }

    $startImage = $hasNok ? 'Nokafschuining.png' : 'Standaard.png';
    $endImage = $hasCutback ? 'Cutback.png' : 'Einde-paneel.png';

    $parts = [];
    if ($hasLayback && $hasNok) {
        $parts[] = ['type' => 'layback-nok', 'image' => 'Layback-en-nokafschuining.png', 'mm' => $laybackMm];
    } elseif ($hasLayback) {
        $parts[] = ['type' => 'layback', 'image' => 'Layback.png', 'mm' => $laybackMm];
    } else {
        $parts[] = ['type' => 'start', 'image' => $startImage, 'mm' => 0];
    }

    $remainingMm = max(0, $length - $laybackMm - $cutbackMm);
    $middleCount = max(6, min(22, (int) ceil($remainingMm / 800)));
    $tileMm = $middleCount > 0 ? $remainingMm / $middleCount : 0;

    for ($i = 0; $i < $middleCount; $i++) {
        $parts[] = ['type' => 'middle', 'image' => 'Riet-standaard-paneel.png', 'mm' => $tileMm];
    }

    $parts[] = ['type' => $hasCutback ? 'cutback' : 'end', 'image' => $endImage, 'mm' => $cutbackMm];

    $waterstops = $panelValues['waterstops'] ?? [];
@endphp

<div style="max-width: 680px; margin: 0 auto; position: relative; font-family: Arial, sans-serif;">

    <!-- Bovenste afmetingen -->
    <div style="position:absolute; top:5px; left:0; right:0; height:45px; font-size:10px; font-weight:bold; z-index:10;">
        @if($hasVrijeRuimte)
            <div style="position:absolute; left:{{ round($freeSpaceFromLeft * $mmToPx) }}px; text-align:center;">
                {{ $freeSpaceFromLeft }} mm
            </div>
            <div style="position:absolute; left:{{ round(($freeSpaceFromLeft + $laybackMm) * $mmToPx) }}px; text-align:center; width:{{ round($freeSpaceSize * $mmToPx) }}px;">
                {{ $freeSpaceSize }} mm
            </div>
        @endif
    </div>

    <!-- Paneel onderdelen - strak naast elkaar -->
    <div style="position:relative; width:{{ $visualContainerWidth }}px; height:{{ $panelHeightPx }}px; margin-top:50px;
                white-space: nowrap; font-size:0; line-height:0; overflow:hidden; border:1px solid #ddd;">

        @foreach($parts as $part)
            @php
                $widthPx = max(12, round($part['mm'] * $mmToPx));
            @endphp
            <div style="display:inline-block; width:{{ $widthPx }}px; height:{{ $panelHeightPx }}px; margin:0; padding:0; vertical-align:top;">
                <img src="{{ public_path($basePath . $part['image']) }}"
                     style="width:100%; height:100%; object-fit:cover; display:block; margin:0; padding:0;"
                     alt="">
            </div>
        @endforeach
    </div>

    <!-- Vrije Ruimte Overlay -->
    @if($hasVrijeRuimte)
        <div style="position:absolute; top:50px; left:{{ round(($freeSpaceFromLeft + $laybackMm) * $mmToPx) }}px;
                    width:{{ round(($freeSpaceSize * $mmToPx) + 38) }}px; height:{{ $panelHeightPx }}px; z-index:5;">
            <div style="float:left; width:calc(100% - 38px); height:100%; overflow:hidden;">
                <img src="{{ public_path($basePath . 'Vrije-ruimte.png') }}"
                     style="width:100%; height:100%; display:block;" alt="">
            </div>
            <img src="{{ public_path($basePath . 'Vrije-ruimte-einde.png') }}"
                 style="float:left; width:38px; height:{{ $panelHeightPx }}px; display:block;" alt="">
        </div>
    @endif

    <!-- Waterstops -->
    @foreach($waterstops as $ws)
        @php
            $wsType = (int)($ws['type'] ?? 0);
            if (!$wsType) continue;
            $pos = max(300, min($length - 600, (int)($ws['vertical'] ?? 0)));
        @endphp
        <div style="position:absolute; top:50px; left:{{ round($pos * $mmToPx) }}px; width:16px; height:{{ $panelHeightPx }}px; z-index:15;">
            <img src="{{ public_path($basePath . 'Waterstop.png') }}"
                 style="width:16px; height:100%; display:block;" alt="">
        </div>
    @endforeach

    <!-- Totale lengte -->
    @if($totaleLengte)
        <div style="text-align:center; font-weight:bold; margin-top:15px; font-size:13px;">
            {{ $totaleLengte }} mm
        </div>
    @endif
</div>
