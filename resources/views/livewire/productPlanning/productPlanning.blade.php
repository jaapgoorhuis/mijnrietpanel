
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

    <div class="max-w-9xl mx-auto sm:px-6 lg:px-8">

        {{-- Alerts --}}
        @if(Session::has('success'))
            <div id="alert-3" class="flex items-center p-4 mb-4 text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
                <svg class="shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                </svg>
                <span class="sr-only">Info</span>
                <div class="ms-3 text-sm font-medium">{{ session('success') }}</div>
                <button type="button" class="ms-auto -mx-1.5 -my-1.5 bg-green-50 text-green-500 rounded-lg focus:ring-2 focus:ring-green-400 p-1.5 hover:bg-green-200 inline-flex items-center justify-center h-8 w-8 dark:bg-gray-800 dark:text-green-400 dark:hover:bg-gray-700" data-dismiss-target="#alert-3" aria-label="Close">
                    <span class="sr-only">Close</span>
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                </button>
            </div>
        @endif

        @if(Session::has('error'))
            <div id="alert-3" class="flex items-center p-4 mb-4 text-white rounded-lg bg-red-500 dark:bg-gray-800 dark:text-red-400" role="alert">
                <svg class="shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                </svg>
                <span class="sr-only">Info</span>
                <div class="ms-3 text-sm font-medium">{{ session('error') }}</div>
                <button type="button" class="ms-auto -mx-1.5 -my-1.5 bg-red-500 text-white rounded-lg focus:ring-2 focus:ring-red-400 p-1.5 hover:bg-red-200 inline-flex items-center justify-center h-8 w-8 dark:bg-gray-800 dark:text-red-400 dark:hover:bg-gray-700" data-dismiss-target="#alert-3" aria-label="Close">
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

        {{-- 📅 Datumreeks selector + print button --}}
        <div class="mb-4 flex items-center gap-3">
            <label for="startDate" class="font-medium">Van:</label>
            <input type="date" id="startDate" wire:model="printStartDate" class="border rounded px-2 py-1">

            <label for="endDate" class="font-medium">Tot:</label>
            <input type="date" id="endDate" wire:model="printEndDate" class="border rounded px-2 py-1">

            <button wire:click="downloadOrdersZip" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                Download orders
            </button>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="flex">
                {{-- Ongeplande orders + blokkeer dagen --}}
                <div id="external-orders-list" class="w-1/4 p-4 border" data-outside="true">
                    <button wire:click="showSettingModal()"><i class="fa-solid fa-cog"></i></button>
                    <h2 class="fc-toolbar-title">Ongeplande Orders</h2>
                    @foreach($unplannedOrders as $order)
                        @php
                            $bgColor = $order->kerndikte_color ?? '#6b7280'; // fallback (grijs)
                            $textColor = getContrastColor($bgColor);
                        @endphp
                        <div class="fc-event p-2 mb-2 cursor-pointer"
                             style="background-color: {{ $bgColor }}; color: {{ $textColor }};"
                             data-id="{{ $order->id }}"
                             data-title="{{ $order->klantnaam . ' ' . $order->project_naam . ' (' . $order->total_m2 . ' m²)' }}"
                             data-color="{{ $bgColor }}">
                            {{ $order->klantnaam. ' ' . $order->project_naam  }} ({{ $order->total_m2 }} m²)
                        </div>
                    @endforeach
                    <br/><br/>
                    <h3>Blokkeer dag</h3>
                    <div id="blocked-days-list" data-outside="true">
                        <div class="fc-event bg-red-500 text-white cursor-move"
                             data-id="block-{{ time() }}"
                             data-title="Blokkeer dag"
                             data-type="manual-block">
                            Sleep om dag te blokkeren
                        </div>
                    </div>
                </div>

                {{-- Kalender --}}
                <div
                    id="calendar" class="w-3/4" wire:ignore
                    data-events='@json($this->getOrders())'
                    data-max-m2="{{ $settings->max_m2_per_day }}"
                    data-events-url="{{ route('livewire.product-planning.events') }}"
                    data-blocked-week-days='@json($settings->blocked_days)'>
                </div>
            </div>
        </div>
    </div>



    <!-- Limiet overschreden modal -->
    <div id="limitModal" wire:ignore.self class="overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full hidden" aria-modal="true" role="dialog">
        <div class="relative p-4 w-full max-w-lg max-h-full">
            <div class="relative bg-white rounded-lg shadow-sm">
                <div class="p-4 md:p-5 text-center">
                    <svg class="mx-auto mb-4 text-gray-400 w-12 h-12" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"></path>
                    </svg>
                    <h3 class="mb-5 text-lg font-normal text-gray-500">
                        Het daglimiet van <strong>{{ $settings->max_m2_per_day }} m²</strong> wordt met
                        <strong>{{ $limitExceededAmount ?? 0 }} m²</strong> overschreden.

                    </h3>

                    <p class="mb-5">Wat wil je doen?</p>

                    <button type="button" wire:click="confirmPlanOrder('nextDay')" class="py-2.5 px-5 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 w-full">
                        Splits op en verplaats naar eerst volgende beschikbare dag
                    </button>
                    <button type="button" wire:click="confirmPlanOrder('sameDay')" class="py-2.5 px-5 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 w-full">
                        Doorgaan op dezelfde dag (overschrijdt het limiet)
                    </button>
                    <button type="button" wire:click="confirmPlanOrder('split')" class="py-2.5 px-5 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 w-full">
                        Splitsen
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="settingModal" wire:ignore.self class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">

        ```
        <div class="bg-white rounded-xl shadow-xl w-full max-w-xl">

            <!-- Header -->
            <div class="flex items-center justify-between border-b px-6 py-4">
                <h2 class="text-lg font-semibold">Productplanning instellingen</h2>

                <button onclick="document.getElementById('settingModal').classList.add('hidden')"
                        class="text-gray-400 hover:text-gray-600">
                    ✕
                </button>
            </div>

            <!-- Body -->
            <div class="p-6 space-y-6">

                <!-- Geblokkeerde dagen -->
                <div>
                    <label class="block text-sm font-medium mb-2">
                        Geblokkeerde dagen
                    </label>

                    <select wire:model="blockedDays" multiple class="w-full border rounded-lg p-2">
                        <option value="zaterdag">Zaterdag</option>
                        <option value="zondag">Zondag</option>
                    </select>

                    <p class="text-xs text-gray-500 mt-1">
                        Houd CTRL / CMD ingedrukt om meerdere dagen te selecteren
                    </p>
                </div>


                <!-- Limiet -->
                <div>
                    <label class="block text-sm font-medium mb-2">
                        Limiet m² per dag
                    </label>

                    <input type="number"
                           wire:model="max_m2_per_day"
                           class="w-full border rounded-lg p-2"
                           placeholder="Bijv. 120">
                </div>


                <!-- Kerndikte kleuren -->
                <div>
                    <label class="block text-sm font-medium mb-3">
                        Kleur per kerndikte
                    </label>

                    <div class="grid grid-cols-2 gap-4">

                        @foreach($this->coreThickness as $kerndikte)

                            <div class="flex items-center justify-between border rounded-lg p-3">
                                <span>{{ $kerndikte->kerndikte }}</span>

                                <input
                                    type="color"
                                    wire:model="coreThicknessColors.{{ $kerndikte->id }}"
                                    class="w-10 h-10 p-0 border-0"
                                >
                            </div>
                        @endforeach

                    </div>
                </div>

            </div>


            <!-- Footer -->
            <div class="flex justify-end gap-3 border-t px-6 py-4">

                <button onclick="document.getElementById('settingModal').classList.add('hidden')"
                        class="px-4 py-2 border rounded-lg hover:bg-gray-100">
                    Annuleren
                </button>

                <button wire:click="saveSettings"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Opslaan
                </button>

            </div>

        </div>
        ```

    </div>

    <div wire:ignore x-data="{ open: @entangle('showBlockedModal') }">
        <div x-show="open" x-cloak class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50 transition-opacity">
            <div class="bg-white p-4 rounded w-96">
                <h2 class="text-lg font-bold mb-2">Pas titel aan</h2>
                <input type="text" wire:model="editingBlockedTitle" class="w-full border px-2 py-1 rounded mb-4">
                <div class="flex justify-end gap-2">
                    <button @click="open = false" class="px-4 py-2 border rounded">Annuleren</button>
                    <button wire:click="updateBlockedTitle" class="px-4 py-2 bg-blue-600 text-white rounded">Opslaan</button>
                </div>
            </div>
        </div>
    </div>

    <div id="limitDecisionModal" class="hidden fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white p-4 rounded shadow">
            <p class="modal-message mb-4"></p>
            <button class="btn-override bg-green-500 text-white px-4 py-2 mr-2 rounded">Overschrijven</button>
            <button class="btn-split bg-yellow-500 text-white px-4 py-2 rounded">Splitsen</button>
        </div>
    </div>

</div>





<script>
    window.blockedDays = @json($settings->blocked_days); // vaste dagen blokkeren
    window.blockedDates = @json($blockedDates ?? []);     // losse datums
</script>
