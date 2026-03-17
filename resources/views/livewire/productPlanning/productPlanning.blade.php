<x-slot name="header">
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
            <li class="inline-flex items-center">
                <a href="/dashboard" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-[#C0A16E]">
                    Mijn rietpanel
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fa-solid fa-angle-right"></i>
                    <p class="ms-1 text-sm font-medium text-gray-700 md:ms-2">Productplanning</p>
                </div>
            </li>
        </ol>
    </nav>
</x-slot>

<div class="py-12">
    <input type="hidden" wire:model="limitExceededOrder">
    <input type="hidden" wire:model="limitExceededDate">

    <div class="max-w-9xl mx-auto sm:px-6 lg:px-8 relative">

        {{-- Alerts --}}
        @if(Session::has('success'))
            <div id="alert-success" class="flex items-center p-4 mb-4 text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
                <svg class="shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                </svg>
                <span class="sr-only">Info</span>
                <div class="ms-3 text-sm font-medium">{{ session('success') }}</div>
                <button type="button" class="ms-auto -mx-1.5 -my-1.5 bg-green-50 text-green-500 rounded-lg focus:ring-2 focus:ring-green-400 p-1.5 hover:bg-green-200 inline-flex items-center justify-center h-8 w-8" data-dismiss-target="#alert-success" aria-label="Close">
                    <span class="sr-only">Close</span>
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                </button>
            </div>
        @endif

        @if(Session::has('error'))
            <div id="alert-error" class="flex items-center p-4 mb-4 text-white rounded-lg bg-red-500" role="alert">
                <svg class="shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                </svg>
                <span class="sr-only">Info</span>
                <div class="ms-3 text-sm font-medium">{{ session('error') }}</div>
                <button type="button" class="ms-auto -mx-1.5 -my-1.5 bg-red-500 text-white rounded-lg focus:ring-2 focus:ring-red-400 p-1.5 hover:bg-red-200 inline-flex items-center justify-center h-8 w-8" data-dismiss-target="#alert-error" aria-label="Close">
                    <span class="sr-only">Close</span>
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                </button>
            </div>
        @endif

        @php
            function getContrastColor($hexColor) {
                if (!$hexColor) return 'black';

                // verwijder #
                $color = ltrim($hexColor, '#');

                // short hex (#fff)
                if (strlen($color) === 3) {
                    $r = hexdec($color[0].$color[0]);
                    $g = hexdec($color[1].$color[1]);
                    $b = hexdec($color[2].$color[2]);
                } elseif (strlen($color) === 6) {
                    $r = hexdec(substr($color, 0, 2));
                    $g = hexdec(substr($color, 2, 2));
                    $b = hexdec(substr($color, 4, 2));
                } else {
                    return 'black'; // fallback
                }

                // brightness formule
                $brightness = ($r * 299 + $g * 587 + $b * 114) / 1000;

                return $brightness > 155 ? 'black' : 'white';
            }
        @endphp

        <div class="mb-4 flex items-center gap-3">
            <label for="startDate" class="font-medium">Van:</label>
            <input type="date" id="startDate" wire:model="printStartDate" class="border rounded px-2 py-1">

            <label for="endDate" class="font-medium">Tot:</label>
            <input type="date" id="endDate" wire:model="printEndDate" class="border rounded px-2 py-1">

            <button wire:click="downloadOrders" class="w-full sm:w-auto text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2">
                Download orders
            </button>
            <button wire:click="downloadPakketlijst" class="w-full sm:w-auto text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2">
                Download pakketlijsten
            </button>
            <button wire:click="downloadFabriekslijst" class="w-full sm:w-auto text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2">
                Download fabriekslijsten
            </button>
        </div>

        <div class="flex relative bg-white">
            {{-- Linker sidebar: fixed / sticky --}}
            <div id="external-orders-list"
                 class="w-1/4 p-4 border bg-white fixed top-16 bottom-0 overflow-y-auto z-10">
                <button wire:click="showSettingModal()"><i class="fa-solid fa-cog"></i></button>
                <h2 class="fc-toolbar-title mb-4">Ongeplande Orders</h2>

                @foreach($unplannedOrders as $order)
                    @php
                        $bgColor = $order->kerndikte_color ?? '#6b7280';
                        $textColor = getContrastColor($bgColor);
                    @endphp
                    <div class="fc-event p-2 mb-2 cursor-pointer"
                         style="background-color: {{ $bgColor }}; color: {{ $textColor }}; border:1px solid rgba(0,0,0,0.6); box-shadow: 0 0 0 1px rgba(255,255,255,0.6) inset;"
                         data-id="{{ $order->id }}"
                         data-title="{{ $order->klantnaam . ' ' . $order->project_naam . ' (' . $order->total_m2 . ' m²)' }}"
                         data-color="{{ $bgColor }}">
                        {{ $order->klantnaam }} {{ $order->project_naam }} ({{ $order->total_m2 }} m²)
                    </div>
                @endforeach

                <br/><br/>
                <h3>Blokkeer dag</h3>
                <div id="blocked-days-list" data-outside="true">
                    <div class="fc-event p-2 mb-2 cursor-pointer"
                         style="background-color: #dc3545; color: white; border: 2px solid rgba(0,0,0,0.6); border-radius: 4px;"
                         data-id="block-{{ time() }}"
                         data-title="Blokkeer dag"
                         data-type="manual-block">
                        Sleep om dag te blokkeren
                    </div>
                </div>
            </div>

            {{-- Kalender rechts: schuif naar rechts ivm fixed sidebar --}}
            <div id="calendar" class="w-3/4" wire:ignore
                 data-events='@json($this->getOrders())'
                 data-max-m2="{{ $settings->max_m2_per_day }}"
                 data-events-url="{{ route('livewire.product-planning.events') }}"
                 data-blocked-week-days='@json($settings->blocked_days)'>
            </div>
        </div>
    </div>
</div>

<script>
    window.blockedDays = @json($settings->blocked_days);
    window.blockedDates = @json($blockedDates ?? []);
</script>
