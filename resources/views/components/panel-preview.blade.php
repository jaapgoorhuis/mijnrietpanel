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
    $panelHeightPx = 260;

    $length = max(1, (int) $totaleLengte);
    $visualLength = max(5000, $length);

    $startImage = match (true) {
        $hasNok => 'Nokafschuining.png',
        default => 'Standaard.png',
    };

    $middleImage = 'Riet-standaard-paneel.png';
    $endImage = $hasCutback ? 'Cutback.png' : 'Einde-paneel.png';

    $middleCount = max(10, min(20, (int) ceil($visualLength / 700)));

    $cutbackMm = (int) ($panelValues['2'] ?? 0);
    $cutbackMm = $hasCutback ? max(20, min(200, $cutbackMm ?: 20)) : 0;

    $cutbackWidthPx = $hasCutback ? 50 : 0;

    $freeSpaceFromLeft = (int) ($panelValues['4_1'] ?? ($hasLayback ? 500 : 300));
    $freeSpaceSize = (int) ($panelValues['4_2'] ?? 0);


    if ($hasVrijeRuimte) {

        // Alleen visualisatie begrenzen
        $freeSpaceFromLeft = max(
            0,
            min($length, $freeSpaceFromLeft)
        );


        $maxAllowedSize = max(
            0,
            $length - $freeSpaceFromLeft - 300
        );


        $freeSpaceSize = max(
            0,
            min(
                $freeSpaceSize,
                $maxAllowedSize
            )
        );
    }

    $laybackVisualOffsetMm = $hasLayback ? (int) ($panelValues['1'] ?? 0) : 0;


    $laybackMm = $hasLayback ? $laybackVisualOffsetMm : 0;
    $remainingMm = max(0, $length - $laybackMm - $cutbackMm);
    $tileMm = $middleCount > 0 ? $remainingMm / $middleCount : 0;

    $parts = [];

    if ($hasLayback && $hasNok) {
        $parts[] = ['type' => 'layback-nok', 'image' => 'Layback-en-nokafschuining.png', 'mm' => $laybackMm];
    } elseif ($hasLayback) {
        $parts[] = ['type' => 'layback', 'image' => 'Layback.png', 'mm' => $laybackMm];
    } else {
        $parts[] = ['type' => 'image', 'image' => $startImage, 'mm' => 0];
    }

    for ($i = 0; $i < $middleCount; $i++) {
        $parts[] = ['type' => 'image', 'image' => $middleImage, 'mm' => $tileMm];
    }

    $parts[] = [
        'type' => $hasCutback ? 'cutback' : 'image',
        'image' => $endImage,
        'mm' => $hasCutback ? $cutbackMm : 0,
    ];

    $waterstops = $panelValues['waterstops'] ?? [];

    $vrEndWidthPx = 42;

    $laybackWidthPx = $hasLayback ? 50 : 0;

    $waterstopCount = collect($waterstops)
    ->filter(fn($ws) => !empty($ws['type']))
    ->count();

    $dimensionRows = 0;

    if ($waterstopCount) {
        $dimensionRows += $waterstopCount;
    }

    if ($hasNok) {
        $dimensionRows++;
    }

    $dimensionHeight = max(60, ($dimensionRows * 25) + 40);

@endphp

<div
    id="panel-render-{{ $index }}"
    class="relative w-full max-w-full overflow-visible pt-24"
    data-free-space-from-left="{{ $hasVrijeRuimte ? $freeSpaceFromLeft : 0 }}"
    data-free-space-size="{{ $hasVrijeRuimte ? $freeSpaceSize : 0 }}"
    data-layback-mm="{{ $laybackMm }}"
    data-has-vrije-ruimte="{{ $hasVrijeRuimte ? 1 : 0 }}"
    x-data="{
        scale: 1,
        panelHeight: {{ $panelHeightPx }},
        mmMap: [],
        _mo: null,

        // ---------------------------------------------------------
        // Bouwt cumulatieve mm -> px tabel op basis van de ECHTE
        // breedte van elk gerenderd onderdeel. Leest data-mm LIVE
        // van de DOM, dus altijd de meest recente server-waarde.
        // ---------------------------------------------------------
        buildMmMap() {
            const parts = this.$refs.inner.querySelectorAll('.panel-part');
            let cumMm = 0, cumPx = 0;
            const map = [];

            parts.forEach(el => {
                const mm = parseFloat(el.dataset.mm) || 0;
                const px = el.offsetWidth || 0;

                map.push({
                    mmStart: cumMm, mmEnd: cumMm + mm,
                    pxStart: cumPx, pxEnd: cumPx + px,
                });

                cumMm += mm;
                cumPx += px;
            });

            this.mmMap = map; // reactieve property -> triggert herrendering afhankelijke expressies
        },

        mmToPx(targetMm) {
            if (!this.mmMap.length) return 0;

            let seg = this.mmMap.find(s => targetMm >= s.mmStart && targetMm <= s.mmEnd);

            if (!seg) {
                seg = targetMm < this.mmMap[0].mmStart
                    ? this.mmMap[0]
                    : this.mmMap[this.mmMap.length - 1];
            }

            const span = (seg.mmEnd - seg.mmStart) || 1;
            const ratio = (targetMm - seg.mmStart) / span;

            return seg.pxStart + ratio * (seg.pxEnd - seg.pxStart);
        },

        // Free space leest LIVE uit data-* op het root element,
        // dus geen bevroren PHP-getallen meer.
       freeSpaceLeftPx() {
            const values = this.$wire.panelValues?.[{{ $index }}] ?? {};
            const options = this.$wire.selectedPanelOption?.[{{ $index }}] ?? [];

            if (!options.includes(4)) return 0;

          const fromLeft = Math.max(
                0,
                parseFloat(values['4_1']) || 0
            );
            const laybackMm = options.includes(1) ? (parseFloat(values['1']) || 0) : 0;

            return this.mmToPx(fromLeft + laybackMm);
        },
        freeSpaceRightPx() {

    const values = this.$wire.panelValues?.[{{ $index }}] ?? {};
    const options = this.$wire.selectedPanelOption?.[{{ $index }}] ?? [];

    if (!options.includes(4)) return 0;


    const length = parseFloat(
        this.$wire.totaleLengte?.[{{ $index }}]
    ) || 0;


    const fromLeft = Math.max(
        0,
        parseFloat(values['4_1']) || 0
    );


    let size = parseFloat(values['4_2']);

    if (!Number.isFinite(size) || size < 0) {
        size = 0;
    }


    const maxSize = Math.max(
        0,
        length - fromLeft - 300
    );


    size = Math.min(
        size,
        maxSize
    );


    const laybackMm = options.includes(1)
        ? (parseFloat(values['1']) || 0)
        : 0;


    return this.mmToPx(
        fromLeft + laybackMm + size
    );
},
        freeSpaceWidthPx() {
            return Math.max(0, this.freeSpaceRightPx() - this.freeSpaceLeftPx());
        },

        resize() {
            this.$nextTick(() => {
                const container = this.$refs.container;
                const inner = this.$refs.inner;

                if (!container || !inner) return;

                this.buildMmMap();

                const naturalWidth = inner.scrollWidth;
                const containerWidth = container.clientWidth;

                if (!naturalWidth || !containerWidth) return;

                const fitScale = containerWidth / naturalWidth;

                this.scale = Math.min(1, Math.max(0.55, fitScale));
                this.panelHeight = {{ $panelHeightPx }} * this.scale;
            });
        },

        init() {
            this.resize();

            new ResizeObserver(() => this.resize()).observe(this.$refs.container);
            new ResizeObserver(() => this.resize()).observe(this.$refs.inner);
            window.addEventListener('resize', () => this.resize());

            // Reactief herrenderen zodra de relevante Livewire-properties
            // wijzigen. Geen MutationObserver/data-* meer nodig: $wire is
            // Alpine's ingebouwde, altijd-actuele brug naar Livewire state.
            this.$watch(
                () => JSON.stringify(this.$wire.panelValues?.[{{ $index }}] ?? {}),
                () => this.resize()
            );

            this.$watch(
                () => (this.$wire.selectedPanelOption?.[{{ $index }}] ?? []).join(','),
                () => this.resize()
            );

            this.$watch(
                () => this.$wire.totaleLengte?.[{{ $index }}],
                () => this.resize()
            );
        }
    }"
    x-init="init()"
>
    <div class="absolute top-[20px] left-0 w-full h-12 text-[10px] md:text-[11px] font-bold pointer-events-none">

        @if($hasVrijeRuimte)
            <div
                class="absolute text-center h-[45px]"
                style="top:0px; left:0;"
                :style="'width: ' + (freeSpaceLeftPx() * scale) + 'px;'"
            >
                {{-- tekst boven --}}
                <div class="absolute left-0 bottom-[15px] w-full leading-none break-words text-center">

                    {{ $freeSpaceFromLeft }} {{ __('messages.mm') }}
                </div>

                {{-- lijn vaste positie --}}
                <div class="absolute left-0 top-[35px] w-full">
                    <div class="flex items-center w-full">
                        <span>|</span>
                        <span class="flex-1 border-t border-black"></span>
                        <span>|</span>
                    </div>
                </div>
            </div>


            <div
                class="absolute text-center h-[45px]"
                :style="`
            top:0px;
            left:${freeSpaceLeftPx() * scale}px;
            width:${freeSpaceWidthPx() * scale}px;
        `"
            >
                {{-- tekst boven --}}
                <div class="absolute left-0 bottom-[15px] w-full leading-none whitespace-nowrap">
                    {{ $freeSpaceSize }} {{ __('messages.mm') }}
                </div>

                {{-- lijn vaste positie --}}
                <div class="absolute left-0 top-[35px] w-full">
                    <div class="flex items-center w-full">
                        <span>|</span>
                        <span class="flex-1 border-t border-black"></span>
                        <span>|</span>
                    </div>
                </div>
            </div>
        @endif

    </div>


    <div class="w-full max-w-full overflow-visible">
        <div
            x-ref="container"
            class="relative w-full overflow-visible"
            :style="'height: ' + (panelHeight + {{ $dimensionHeight }}) + 'px;'"
        >
            <div
                x-ref="inner"
                class="absolute left-0 top-0 flex items-start origin-top-left"
                :style="'transform: scale(' + scale + '); width: max-content;'"
            >

                @foreach($parts as $i => $part)
                    @if($part['type'] === 'cutback')
                        <div
                            class="panel-part"
                            data-mm="{{ $part['mm'] }}"
                            style="
                                height: {{ $panelHeightPx }}px;
                                width: {{ $cutbackWidthPx }}px;
                                min-width: {{ $cutbackWidthPx }}px;
                                flex: 0 0 {{ $cutbackWidthPx }}px;
                                background-image: url('{{ asset($basePath . $part['image']) }}');
                                background-repeat: repeat-x;
                                background-size: auto {{ $panelHeightPx }}px;
                                background-position: left top;
                            "
                        ></div>

                    @elseif($part['type'] === 'layback' || $part['type'] === 'layback-nok')
                        <div
                            class="panel-part relative"
                            data-mm="{{ $part['mm'] }}"
                            style="
                                height: {{ $panelHeightPx }}px;
                                width: {{ $laybackWidthPx }}px;
                                flex: 0 0 {{ $laybackWidthPx }}px;
                            "
                        >
                            <img
                                src="{{ asset($basePath . $part['image']) }}"
                                alt="Layback"
                                class="block max-w-none"
                                style="height: {{ $panelHeightPx }}px; width: {{ $laybackWidthPx }}px;"
                            >

                            <div
                                class="absolute text-center font-bold"
                                :style="`
                                    left:0;
                                    top:{{ ($panelHeightPx / 2) - 60 }}px;
                                    width:100%;
                                    z-index:50;
                                    font-size: ${Math.max(8, 11 / scale)}px;
                                `"
                            >
                                <div class="flex items-center w-full">
                                    <span>|</span>
                                    <span class="flex-1 border-t border-black"></span>
                                    <span>|</span>
                                </div>

                                <div class="leading-none mt-1 whitespace-normal break-words">
                                    LB {{ $panelValues['1'] ?? 0 }} {{ __('messages.mm') }}
                                </div>
                            </div>
                        </div>

                    @else
                        <div
                            class="panel-part"
                            data-mm="{{ $part['mm'] }}"
                            style="flex: 0 0 auto; @if($i > 0) margin-left: -1px; @endif"
                        >
                            <img
                                src="{{ asset($basePath . $part['image']) }}"
                                alt="Paneel onderdeel"
                                class="block max-w-none"
                                style="height: {{ $panelHeightPx }}px; width: auto;"
                            >
                        </div>
                    @endif
                @endforeach

                @if($hasVrijeRuimte)
                    <div
                        class="absolute top-0 flex items-start overflow-visible"
                        :style="'left: ' + freeSpaceLeftPx() + 'px; width: calc(' + freeSpaceWidthPx() + 'px + {{ $vrEndWidthPx }}px); height: {{ $panelHeightPx }}px; z-index: 10;'"
                    >
                        <div
                            class="absolute left-0 top-0 pointer-events-none"
                            style="width: 100%; height: 25px; background: white; z-index: -1;"
                        ></div>

                        <div
                            class="bg-white overflow-hidden"
                            style="height: {{ $panelHeightPx }}px; width: calc(100% - {{ $vrEndWidthPx }}px); flex: 1 1 auto;"
                        >
                            <img
                                src="{{ asset($basePath . 'Vrije-ruimte.png') }}"
                                alt="Vrije ruimte"
                                class="block"
                                style="height: {{ $panelHeightPx }}px; width: 100%;"
                            >
                        </div>

                        <img
                            src="{{ asset($basePath . 'Vrije-ruimte-einde.png') }}"
                            alt="Vrije ruimte einde"
                            class="block max-w-none"
                            style="
                                height: {{ $panelHeightPx }}px;
                                width: {{ $vrEndWidthPx }}px;
                                flex: 0 0 {{ $vrEndWidthPx }}px;
                                margin-left: -1px;
                            "
                        >
                    </div>
                @endif

                    @foreach($waterstops as $wsIndex => $waterstop)
                        @php
                            $wsType = (int) ($waterstop['type'] ?? 0);

                            if (!$wsType) {
                                continue;
                            }

                           $wsFromLeftInput = (int) ($waterstop['vertical'] ?? 0);

                            $maxWaterstopPosition = $length - 600;

                            $wsFromLeft = min(
                                max(300, $wsFromLeftInput),
                                $maxWaterstopPosition
                            );

                            $maxWaterstopPosition = max(300, $length - 600);

                            $wsFromLeft = max(
                                300,
                                min($wsFromLeft, $maxWaterstopPosition)
                            );
                            $wsFromCenter = (int) ($waterstop['horizontal'] ?? 0);

                            // Werkelijke hoogte die $panelHeightPx representeert (incl.
                            // perspectief-effect van de render).
                            $panelRealHeightMm = 1365.43;
                            $mmToPxVertical = $panelHeightPx / $panelRealHeightMm;

                            // Marge bovenaan de afbeelding voordat het meetbare werkgebied
                            // Marge bovenaan de afbeelding voordat het meetbare werkgebied
                            // begint (perspectief-correctie).
                            $topOffsetMm = 20;
                            $topOffsetPx = $topOffsetMm * $mmToPxVertical;

                            // Alle waterstops worden gecentreerd binnen een virtueel
                            // werkgebied van 960mm (= de grootste waterstop-maat). Bij
                            // type 960 is de marge dus 0, bij kleinere types symmetrisch
                            // verdeeld boven en onder.
                            $waterstopWorkAreaMm = 960;
                            $marginMm = max(0, ($waterstopWorkAreaMm - $wsType) / 2);

                            $wsHeightPx = $wsType * $mmToPxVertical;
                            $wsHeightPx = max(20, min($panelHeightPx, $wsHeightPx));

                            // horizontal: positief = omhoog (kleinere top), negatief = omlaag
                            $wsTopPx = $topOffsetPx + ($marginMm * $mmToPxVertical) - ($wsFromCenter * $mmToPxVertical);
                            $wsTopPx = max(0, min($panelHeightPx - $wsHeightPx, $wsTopPx));

                            $wsMmPosition = $wsFromLeft + $laybackMm;

$waterstopWidthPx = 20;
$waterstopLineOffsetMm = -20;
$wsLineEndMm = $wsMmPosition + $waterstopLineOffsetMm;
                        @endphp

                        {{-- Waterstop afbeelding --}}
                        <div
                            class="absolute pointer-events-none"
                            data-mm="{{ $wsMmPosition }}"
                            data-waterstop-width="20"
                            :style="'left: ' + mmToPx(parseFloat($el.dataset.mm)) + 'px; top:{{ $wsTopPx }}px; width:20px; height:{{ $wsHeightPx }}px; z-index:20;'"
                        >
                            <img
                                src="{{ asset($basePath . 'Waterstop.png') }}"
                                alt="Waterstop"
                                class="block max-w-none"
                                style="width:20px; height:100%; object-fit:fill;"
                            >
                        </div>

                        {{-- Maat vanaf links --}}
                        <div
                            class="absolute text-center font-bold pointer-events-none"
                            data-mm="{{ $wsMmPosition }}"
                            :style="`
                                left: 0;
                               top: ${
                                    {{ $panelHeightPx }} +
                                    ((20 + ({{ $wsIndex }} * 20)) / scale)
                                }px;
                                z-index: 40;
                               width: ${
                                    mmToPx(parseFloat($el.dataset.mm)) + 20
                                }px;
                                font-size: ${Math.max(8, 10 / scale)}px;
                            `"
                            >
                            <div class="flex items-center w-full">
                                <span>|</span>
                                <span class="flex-1 border-t border-black"></span>
                                <span>|</span>
                            </div>

                            <div class="leading-tight break-words text-center px-1 rounded">

                                {{ $wsFromLeft }} {{ __('messages.mm') }}
                            </div>
                        </div>
                    @endforeach

                @if($hasNok)
                    <div
                        class="absolute left-2 pointer-events-none"
                        style="
                              top: {{ $panelHeightPx - 72 }}px;
                            width: 65px;
                            margin-left:-15px;
                            height: 40px;
                            z-index: 30;
                        "
                    >
                        <svg width="100" height="80" viewBox="0 0 65 42" xmlns="http://www.w3.org/2000/svg">
                            <defs>
                                <marker
                                    id="arrow-{{ $index }}"
                                    markerWidth="3.5" markerHeight="3.5"
                                    refX="3" refY="1.75" orient="auto"
                                >
                                    <path d="M0,0 L3.5,1.75 L0,3.5 Z" fill="#000"/>
                                </marker>
                            </defs>

                            <line x1="6" y1="2" x2="6" y2="32" stroke="#000" stroke-width="1.5"/>
                            <line x1="8" y1="4" x2="26" y2="30" stroke="#000" stroke-width="1.5"/>

                            <path
                                d="M8 4 Q18 18 26 30"
                                fill="none"
                                stroke="#000"
                                stroke-width="1.5"
                            />

                            <text
                                x="10"
                                y="41"
                                font-size="7"
                                font-family="sans-serif"
                                font-weight="700"
                            >
                                {{ $panelValues['3'] ?? 0 }}°
                            </text>
                        </svg>
                    </div>
                @endif

                    @if($totaleLengte)
                        <div
                            class="absolute text-center font-bold pointer-events-none"
                            :style="`
            left: 0;
         top: {{ $panelHeightPx + 10 }}px;
            width: 100%;
            z-index: 100;
            font-size: ${Math.max(8, 11 / scale)}px;
        `"
                        >
                            <div class="flex items-center w-full">
                                <span>|</span>
                                <span class="flex-1 border-t border-black"></span>
                                <span>|</span>
                            </div>

                            <div class="leading-none whitespace-nowrap mt-1">
                                {{ $totaleLengte }} mm
                            </div>
                        </div>
                    @endif

                @if($hasCutback)
                        <div
                            class="absolute text-center font-bold"
                            :style="`
                                right:0;
                                top:{{ $panelHeightPx - 40 }}px;
                                width:{{ $cutbackWidthPx }}px;
                                z-index:50;
                                font-size: ${Math.max(8, 11 / scale)}px;
                            `"
                        >
                        <div class="flex items-center w-full">
                            <span>|</span>
                            <span class="flex-1 border-t border-black"></span>
                            <span>|</span>
                        </div>

                        <div class="leading-none whitespace-nowrap">
                            CB {{ $cutbackMm }} {{ __('messages.mm') }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

</div>

