{{--
    Gedeeld formulier-gedeelte voor een offerte-/orderregel (paneelopties + waterstops + render-preview).
    Gebruikt in createOfferte, changeOfferte, createOrder en changeOrder — deze vier waren tot nu toe
    bijna letterlijk gekopieerd. Validatie op de paneelopties/waterstop-velden gebruikt overal de
    strengere validateFreeSpace()/validateWaterstopInput() (voorheen alleen in createOrder aanwezig).

    Om dit terug te draaien: vervang in de body hieronder de wire:blur="validateFreeSpace(...)" en
    wire:blur="validateWaterstopInput(...)" aanroepen door wire:blur="normalizePanelOptions({{$index}})".
--}}
@props([
    'index',
    'selectedOptions' => [],
    'panelValues' => [],
    'waterstopEnabled' => false,
    'totaleLengte' => 0,
    'm2' => 0,
    'laybackPrice' => 0,
    'nokafschuiningPrice' => 0,
    'vrijeruimtePrice' => 0,
    'waterstopPrice' => 0,
])

@php
    $waterstopOptions = [
        960 => __('messages.waterstop_960'),
        840 => __('messages.waterstop_840'),
        730 => __('messages.waterstop_730'),
        500 => __('messages.waterstop_500'),
        300 => __('messages.waterstop_300'),
    ];

    $waterstopHorizontalMax = [
        960 => 0,
        840 => 60,
        730 => 115,
        500 => 230,
        300 => 330,
    ];

    $currentVerticalMax = max(300, (int)($totaleLengte ?? 0) - 600);
@endphp

<div class="text-right">
    {{ __('messages.Vierkante meters') }}: <strong>{{ $m2 }} m²</strong>
</div>

<br/><br/><br/>

<div wire:key="panel-line-{{ $index }}" class="flex flex-col lg:flex-row w-full mb-[30px] gap-8 items-start">

    <div class="w-full lg:w-[280px] flex-shrink-0">

        @php
            $tooltips = [
                1 => __('messages.Meerprijs layback') . ' €' . $laybackPrice.',-',
                3 => __('messages.Meerprijs nokafschuining') . ' €' . $nokafschuiningPrice.',-',
                4 => __('messages.Meerprijs vrije ruimte') . ' €' . $vrijeruimtePrice.',-',
            ];
        @endphp

        @foreach([
            1 => __('messages.Layback'),
            2 => __('messages.Cutback'),
            3 => __('messages.Nok afschuining'),
            4 => __('messages.Vrije ruimte')
        ] as $option => $label)
            <label class="cursor-pointer flex flex-col relative mt-[20px]">

                <div
                    wire:click="togglePanelOption({{$index}}, {{$option}})"
                    class="border rounded p-1 w-full relative cursor-pointer
                        {{ in_array($option, $selectedOptions) ? 'border-blue-500 border-2' : '' }}"
                >

                    <img
                        src="{{ asset("storage/images/rietpanel/paneel-$option.png") }}"
                        class="w-full h-[50px] object-contain"
                    >

                    <div class="text-center font-bold mt-1">
                        {{ $label }}
                    </div>

                    @if(isset($tooltips[$option]))
                        <div class="absolute top-1 right-1">
                            <div class="relative inline-block group">
                                <i class="fa-solid fa-circle-info text-gray-600 hover:text-blue-500 cursor-pointer"></i>

                                <div class="absolute right-0 top-full mt-1 w-56 bg-gray-700 text-white text-sm p-2 rounded shadow-lg
                opacity-0 pointer-events-none group-hover:opacity-100 group-hover:pointer-events-auto
                transition-opacity duration-200 z-50">
                                    {{ $tooltips[$option] }}
                                </div>
                            </div>
                        </div>
                    @endif

                </div>
            </label>

            @if(in_array($option, $selectedOptions))
                @if($option == 4)
                    @php
                        $vrijeRuimteMinTop = in_array(1, $selectedOptions) ? 500 : 300;
                    @endphp
                    <label><strong>{{ __('messages.Ruimte bovenkant tot vrije ruimte') }}</strong></label>
                    <div class="relative">
                        <input type="number"
                               wire:model.live.debounce.400ms="panelValues.{{$index}}.4_1"
                               wire:blur="validateFreeSpace({{$index}})"
                               wire:change="updatePanelValues({{$index}}, '4_1')"
                               placeholder="{{ __('messages.Vul waarde in') }}"
                               class="border rounded px-2 py-1 w-full mt-1">

                        <span class="absolute right-2 top-[77%] -translate-y-1/2 text-gray-500 text-sm pointer-events-none">
                            {{ __('messages.mm') }}
                        </span>
                    </div>
                    <div class="text-xs text-gray-500 mt-1">
                        {{ __('messages.Ruimte bovenkant tot vrije ruimte moet 0 of minimaal ') }}{{ $vrijeRuimteMinTop }} {{ __('messages.mm') }}
                    </div>
                    @error('panelValues.'.$index.'.4_1')
                    <div class="text-red-500 text-sm">{{ $message }}</div>
                    @enderror

                    <label><strong>{{ __('messages.Vrije ruimte') }}</strong></label>
                    <div class="relative">
                        <input type="number"
                               wire:model.live.debounce.400ms="panelValues.{{$index}}.4_2"
                               wire:blur="validateFreeSpace({{$index}})"
                               wire:change="updatePanelValues({{$index}}, '4_2')"
                               placeholder="{{ __('messages.Vul waarde in') }}"
                               class="border rounded px-2 py-1 w-full mt-1">

                        <span class="absolute right-2 top-[77%] -translate-y-1/2 text-gray-500 text-sm pointer-events-none">
                            {{ __('messages.mm') }}
                        </span>
                    </div>
                    <div class="text-red-500 text-sm mt-1">
                        @error('panelValues.'.$index.'.4_2') {{ $message }} @enderror
                    </div>
                @elseif($option == 3)
                    <label><strong>{{ $label }} in graden</strong></label>
                    <div class="relative">
                        <input type="number"
                               wire:model.live.debounce.400ms="panelValues.{{$index}}.{{ $option }}"
                               wire:blur="validateFreeSpace({{$index}})"
                               wire:change="updatePanelValues({{$index}}, {{ $option }})"
                               min="15"
                               max="60"
                               step="1"
                               class="border rounded px-2 py-1 w-full pr-10 mt-1">

                        <span class="absolute right-2 top-[77%] -translate-y-1/2 text-gray-500 text-sm pointer-events-none">
                            &deg;
                        </span>
                    </div>
                    <div class="text-xs text-gray-500 mt-1">
                        {{ __('messages.De nokafschuining moet tussen 15 en 60 graden zijn') }}
                    </div>
                    @error('panelValues.'.$index.'.'.$option)
                    <div class="text-red-500 text-sm">{{ $message }}</div>
                    @enderror
                @else
                    <label><strong>{{ $label }} in {{ __('messages.mm') }}</strong></label>

                    <select
                        wire:model.live.debounce.400ms="panelValues.{{$index}}.{{ $option }}"
                        wire:blur="validateFreeSpace({{$index}})"
                        wire:change="updatePanelValues({{$index}}, {{ $option }})"
                        class="border rounded px-2 py-1 w-full mt-1"
                    >
                        @for($i = 20; $i <= 140; $i += 20)
                            <option value="{{ $i }}">{{ $i }} {{ __('messages.mm') }}</option>
                        @endfor
                    </select>

                    <div class="text-red-500 text-sm mt-1">
                        @error('panelValues.'.$index.'.'.$option) {{ $message }} @enderror
                    </div>
                @endif
            @endif
        </label>
        @endforeach

        <div class="text-red-500 text-sm mt-2">
            @error('totaleLengte.'.$index) {{ $message }} @enderror
        </div>

        <label class="cursor-pointer flex flex-col relative mt-[20px]">

            <div
                wire:click="toggleWaterstopChecked({{$index}})"
                class="border rounded p-1 w-full relative cursor-pointer
    {{ $waterstopEnabled ? 'border-blue-500 border-2' : '' }}"
            >

                <img
                    src="{{ asset('storage/images/rietpanel/paneel-5.png') }}"
                    class="w-full h-[50px] object-contain"
                >

                <div class="text-center font-bold mt-1">
                    {{ __('messages.Waterstop') }}
                </div>

                <div class="absolute top-1 right-1">
                    <div class="relative inline-block group">
                        <i class="fa-solid fa-circle-info text-gray-600 hover:text-blue-500 cursor-pointer"></i>

                        <div class="absolute right-0 top-full mt-1 w-56 bg-gray-700 text-white text-sm p-2 rounded shadow-lg
                opacity-0 pointer-events-none group-hover:opacity-100 group-hover:pointer-events-auto
                transition-opacity duration-200 z-50">
                            {{ __('messages.Meerprijs per waterstop '). ' €' . $waterstopPrice.',-' }}
                        </div>
                    </div>
                </div>

            </div>
        </label>

        @if($waterstopEnabled)
            <div wire:key="waterstop-container-{{ $index }}-{{ $waterstopEnabled ? 1 : 0 }}" class="border rounded p-3 mt-4 bg-gray-50">
                <div class="mt-3 space-y-3">
                    @foreach(($panelValues['waterstops'] ?? []) as $wsIndex => $waterstop)
                        @php
                            $selectedWaterstopType = (int)($waterstop['type'] ?? 0);
                            $currentHorizontalMax = $waterstopHorizontalMax[$selectedWaterstopType] ?? 0;
                        @endphp

                        <div class="border rounded p-3 bg-white">
                            <div class="flex justify-between items-center">
                                <strong>{{ __('messages.Waterstop') }} {{ $wsIndex + 1 }}</strong>

                                <button
                                    type="button"
                                    wire:click="removeWaterstop({{ $index }}, {{ $wsIndex }})"
                                    class="text-red-500 hover:text-red-700"
                                >
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>

                            <label class="block mt-3">
                                <strong>{{ __('messages.Waterstop type') }}</strong>
                            </label>

                            <select
                                wire:model.live="panelValues.{{$index}}.waterstops.{{$wsIndex}}.type"
                                wire:change="normalizePanelOptions({{$index}})"
                                class="border rounded px-2 py-1 w-full mt-1"
                            >
                                <option value="">{{ __('messages.Selecteer') }}...</option>

                                @foreach($waterstopOptions as $value => $text)
                                    <option value="{{ $value }}">
                                        {{ $value }} {{ __('messages.mm') }}
                                    </option>
                                @endforeach
                            </select>

                            @if(!empty($waterstop['type']))
                                <div class="mt-2 rounded-md bg-gray-100 border border-gray-200 p-2 text-sm text-gray-600">
                                    <i class="fa-solid fa-circle-info mr-1 text-gray-500"></i>
                                    {{ $waterstopOptions[(int)$waterstop['type']] ?? '' }}
                                </div>
                            @endif

                            <div class="text-red-500 text-sm mt-1">
                                @error('panelValues.'.$index.'.waterstops.'.$wsIndex.'.type') {{ $message }} @enderror
                            </div>

                            <label class="block mt-3">
                                <strong>{{ __('messages.Positie vanaf bovenzijde tot onderkant waterstop') }}</strong>
                            </label>

                            <div class="relative">
                                <input
                                    type="number"
                                    wire:model.live.debounce.400ms="panelValues.{{$index}}.waterstops.{{$wsIndex}}.vertical"
                                    wire:blur="validateWaterstopInput({{$index}}, {{$wsIndex}})"
                                    min="0"
                                    max="{{ $currentVerticalMax }}"
                                    class="border rounded px-2 py-1 w-full mt-1 pr-10"
                                    placeholder="300"
                                >

                                <span class="absolute right-2 top-[58%] -translate-y-1/2 text-gray-500 text-sm pointer-events-none">
                    {{ __('messages.mm') }}
                </span>
                            </div>

                            <div class="text-xs text-gray-500 mt-1">
                                {{ __('messages.Minimaal 300 mm vanaf bovenzijde, maximaal 600mm vanaf onderzijde paneel') }}

                            </div>

                            <div class="text-red-500 text-sm mt-1">
                                @error('panelValues.'.$index.'.waterstops.'.$wsIndex.'.vertical') {{ $message }} @enderror
                            </div>

                            <label class="block mt-3">
                                <strong>{{ __('messages.Horizontale verplaatsing vanuit midden') }}</strong>
                            </label>

                            <div class="relative">
                                <input
                                    wire:blur="normalizePanelOptions({{$index}})"
                                    type="number"
                                    wire:model.live="panelValues.{{$index}}.waterstops.{{$wsIndex}}.horizontal"
                                    class="border rounded px-2 py-1 w-full mt-1 pr-10"
                                    placeholder="{{ __('messages.0 is midden') }}"
                                    step="1"
                                >

                                <span class="absolute right-2 top-[58%] -translate-y-1/2 text-gray-500 text-sm pointer-events-none">
                    {{ __('messages.mm') }}
                </span>
                            </div>

                            <div class="text-xs text-gray-500 mt-1">
                                {{ __('messages.Negatief is naar links positief is naar rechts') }}.
                                @if(!empty($waterstop['type']))
                                    <br>{{ __('messages.Maximaal') }}: {{ $currentHorizontalMax }} {{ __('messages.mm') }} {{ __('messages.naar links en rechts') }}.
                                @else
                                    <br>{{ __('messages.Selecteer eerst een type waterstop') }}.
                                @endif
                            </div>

                            <div class="text-red-500 text-sm mt-1">
                                @error('panelValues.'.$index.'.waterstops.'.$wsIndex.'.horizontal') {{ $message }} @enderror
                            </div>
                        </div>
                    @endforeach
                </div>

                <button
                    type="button"
                    wire:click="addWaterstop({{ $index }})"
                    class="mt-3 text-white bg-gray-800 hover:bg-gray-900 rounded px-3 py-2 text-sm"
                >
                    <i class="fa fa-plus"></i>
                    {{ __('messages.Waterstop toevoegen') }}
                </button>

                <div class="text-red-500 text-sm mt-2">
                    @error('panelValues.'.$index.'.waterstops') {{ $message }} @enderror
                </div>
            </div>
        @endif
    </div>

    <div class="block lg:hidden text-xs text-gray-500 text-center mb-2 animate-pulse">
        ← {{ __('messages.swipe_horizontal_panel') }} →
    </div>

    <div
        class="w-full lg:flex-1 lg:min-w-0 lg:self-start lg:sticky lg:top-24 lg:h-fit z-20"
        wire:loading.class="opacity-0"
        wire:target="panelValues.{{ $index }}"
    >
        <div class="relative w-full max-w-full overflow-x-auto lg:overflow-visible bg-white">
            <x-panel-preview
                :index="$index"
                :selected-options="$selectedOptions"
                :panel-values="$panelValues"
                :totale-lengte="$totaleLengte"
            />
        </div>
    </div>

</div>
