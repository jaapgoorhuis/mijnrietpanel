
<x-slot name="header">
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
            <li class="inline-flex items-center">
                <a href="/dashboard" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-[#C0A16E]">
                    {{ __('messages.Mijn Rietpanel') }}
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fa-solid fa-angle-right"></i>
                    <a href="/offertes" class="inline-flex items-center md:ms-2 text-sm font-medium text-gray-700 hover:text-[#C0A16E] ">
                        {{ __('messages.Mijn offertes') }}
                    </a>
                </div>
            </li>

            <li>
                <div class="flex items-center">
                    <i class="fa-solid fa-angle-right"></i>
                    <p class="ms-1 text-sm font-medium text-gray-700 md:ms-2">   {{ __('messages.Nieuwe offerte aanmaken') }}</p>
                </div>
            </li>
        </ol>
    </nav>
</x-slot>

<div class="py-12">
    <div class="max-w-9xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-visible shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="grid">
                    <form>
                        <div class="relative">
                            <i wire:click="cancelCreateOfferte()" class="absolute right-0 fa-solid fa-xmark text-xl hover:cursor-pointer"></i>
                        </div>

                        {{ __('messages.Project gegevens') }}
                        <br/><br/>
                        <div class="grid md:grid-cols-2 md:gap-6">
                            <div class="relative z-0 w-full mb-5 group">
                                <label for="klant_naam" class="text-gray-400">   {{ __('messages.Klantnaam') }} *</label>
                                <input type="text"  wire:model="klant_naam" name="klant_naam" id="klant_naam" class="block py-2.5 px-0 w-full text-md text-gray-900 border-0 border-b-2 border-gray-300 appearance-none dark:text-gray-900 dark:border-gray-600 focus:outline-none focus:ring-0 focus:border-b-[#C0A16E]" placeholder=" " required />
                                <div class="text-red-500">@error('klant_naam') {{ $message }} @enderror</div>
                            </div>

                            <div class="relative z-0 w-full mb-5 group">
                                <label for="requested_delivery_date" class="text-gray-400">   {{ __('messages.Gewenste leverdatum') }} *</label>

                                <input
                                    type="text"
                                    class="datepicker block w-full bg-neutral-secondary-medium border-0 border-b-2 border-gray-300 appearance-none dark:text-gray-900 dark:border-gray-600 focus:outline-none focus:ring-0 focus:border-b-[#C0A16E]"
                                    wire:model="requested_delivery_date"
                                    placeholder=" {{ __('messages.Selecteer datum') }}"
                                />
                                <div class="text-red-500">@error('requested_delivery_date') {{ $message }} @enderror</div>
                            </div>

                        </div>

                        <div class="grid md:grid-cols-2 md:gap-6">
                            <div class="relative z-0 w-full mb-5 group">
                                <label for="klant_naam" class="text-gray-400">   {{ __('messages.Projectnaam') }} *</label>
                                <input type="text"  wire:model="project_naam" name="project_naam" id="project_naam" class="block py-2.5 px-0 w-full text-md text-gray-900 border-0 border-b-2 border-gray-300 appearance-none dark:text-gray-900 dark:border-gray-600 focus:outline-none focus:ring-0 focus:border-b-[#C0A16E]" placeholder=" " required />
                                <div class="text-red-500">@error('project_naam') {{ $message }} @enderror</div>
                            </div>
                            <div class="relative z-0 w-full mb-5 group">
                                <label for="referentie" class="text-gray-400">   {{ __('messages.Referentie') }} *</label>
                                <input type="text"  wire:model="referentie" name="referentie" id="referentie" class="block py-2.5 px-0 w-full text-md text-gray-900 border-0 border-b-2 border-gray-300 appearance-none dark:text-gray-900 dark:border-gray-600 focus:outline-none focus:ring-0 focus:border-b-[#C0A16E]" placeholder=" " required />
                                <div class="text-red-500">@error('referentie') {{ $message }} @enderror</div>
                            </div>
                        </div>


                        <div class="grid md:grid-cols-2 md:gap-6">
                            <div class="relative z-0 w-full mb-5 group">
                                <label for="aflever_straat" class="text-gray-400">   {{ __('messages.Aflever straat') }} *</label>
                                <input type="text" wire:model="aflever_straat" name="aflever_straat" id="aflever_straat" class="block py-2.5 px-0 w-full text-md text-gray-900 border-0 border-b-2 border-gray-300 appearance-none dark:text-gray-900 dark:border-gray-600 focus:outline-none focus:ring-0 focus:border-b-[#C0A16E]" placeholder=" " />
                                <div class="text-red-500">@error('aflever_straat') {{ $message }} @enderror</div>
                            </div>
                            <div class="relative z-0 w-full mb-5 group">
                                <label for="aflever_postcode" class="text-gray-400">   {{ __('messages.Aflever postcode') }} *</label>
                                <input type="text" wire:model="aflever_postcode" name="aflever_postcode" id="aflever_postcode" class="block py-2.5 px-0 w-full text-md text-gray-900 border-0 border-b-2 border-gray-300 appearance-none dark:text-gray-900 dark:border-gray-600 focus:outline-none focus:ring-0 focus:border-b-[#C0A16E]" placeholder=" " />
                                <div class="text-red-500">@error('aflever_postcode') {{ $message }} @enderror</div>
                            </div>
                        </div>

                        <div class="grid md:grid-cols-2 md:gap-6">
                            <div class="relative z-0 w-full mb-5 group">
                                <label for="aflever_plaats" class="text-gray-400">{{ __('messages.Aflever plaats') }} *</label>
                                <input type="text" wire:model="aflever_plaats" name="aflever_plaats" id="aflever_plaats" class="block py-2.5 px-0 w-full text-md text-gray-900 border-0 border-b-2 border-gray-300 appearance-none dark:text-gray-900 dark:border-gray-600 focus:outline-none focus:ring-0 focus:border-b-[#C0A16E]" placeholder=" " />
                                <div class="text-red-500">@error('aflever_plaats') {{ $message }} @enderror</div>
                            </div>
                            <div class="relative z-0 w-full mb-5 group">
                                <label for="aflever_land" class="text-gray-400">{{ __('messages.Aflever land') }} *</label>
                                <input type="text" wire:model="aflever_land" name="aflever_land" id="aflever_land" class="block py-2.5 px-0 w-full text-md text-gray-900 border-0 border-b-2 border-gray-300 appearance-none dark:text-gray-900 dark:border-gray-600 focus:outline-none focus:ring-0 focus:border-b-[#C0A16E]" placeholder=" " />
                                <div class="text-red-500">@error('aflever_land') {{ $message }} @enderror</div>
                            </div>
                        </div>

                        <div class="grid md:grid-cols-2 md:gap-6">
                            <div class="relative z-0 w-full mb-5 group">
                                <label for="rietkleur" class="text-gray-400">{{ __('messages.Rietkleur') }} *</label>
                                <select id="rietkleur" wire:model="rietkleur" class="block py-2.5 px-0 w-full text-sm text-gray-500 bg-transparent border-0 border-b-2 border-gray-200 appearance-none dark:text-gray-900 focus:outline-none focus:ring-0 focus:border-gray-200 peer">

                                    <option value="Old look">Old look</option>
                                    <option value="New look">New look</option>

                                </select>
                            </div>
                            <div class="relative z-0 w-full mb-5 group">
                                <label for="kerndikte" class="text-gray-400">{{ __('messages.Kerndikte') }} *</label>
                                <select id="kerndikte" wire:change="updatePrice()" wire:model="kerndikte" class="block py-2.5 px-0 w-full text-sm text-gray-500 bg-transparent border-0 border-b-2 border-gray-200 appearance-none dark:text-gray-400 dark:border-gray-700 focus:outline-none focus:ring-0 focus:border-gray-200 peer">
                                    <option value="" selected>{{ __('messages.Selecteer een kerndikte') }}</option>
                                    @foreach($this->panelTypes as $type)
                                        <option value="{{$type->name}}">{{$type->name}}</option>
                                    @endforeach
                                </select>
                                <div class="text-red-500">@error('kerndikte') {{ $message }} @enderror</div>
                            </div>
                        </div>

                        <div class="grid md:grid-cols-2 md:gap-6">
                            <div class="relative z-0 w-full mb-5 group">
                                <label for="intaker_name" class="text-gray-400">{{ __('messages.Verkoper') }} *</label>
                                <input type="text" wire:model="intaker" name="intaker" id="intaker" class="block py-2.5 px-0 w-full text-md text-gray-900 border-0 border-b-2 border-gray-300 appearance-none dark:text-gray-900 dark:border-gray-600 focus:outline-none focus:ring-0 focus:border-b-[#C0A16E]" placeholder=" " required />
                                <div class="text-red-500">@error('intaker') {{ $message }} @enderror</div>
                            </div>


                        </div>
                        <div class="grid md:grid-cols-2 md:gap-6">
                            <div class="relative z-0 w-full mb-5 group">
                                <label for="toepassing" class="text-gray-400">{{ __('messages.Toepassing') }} *</label>
                                <select id="toepassing" wire:model="toepassing" wire:change="updateBrands()" class="block py-2.5 px-0 w-full text-sm text-gray-500 bg-transparent border-0 border-b-2 border-gray-200 appearance-none dark:text-gray-900 dark:border-gray-700 focus:outline-none focus:ring-0 focus:border-gray-200 peer">

                                    <option value="Dak">{{ __('messages.Dak') }}</option>
                                    <option value="Wand">{{ __('messages.Gevel') }}</option>

                                </select>
                            </div>


                            <div class="relative z-0 w-full mb-5 group">
                                <label for="merk_paneel" class="text-gray-400">{{ __('messages.Merk element') }} *</label>
                                <select @if(count($this->offerteLines)) disabled @endif id="merk_paneel" wire:model="merk_paneel" class="disabled:hover:cursor-not-allowed block py-2.5 px-0 w-full text-sm text-gray-500 bg-transparent border-0 border-b-2 border-gray-200 appearance-none dark:text-gray-900 dark:border-gray-700 focus:outline-none focus:ring-0 focus:border-gray-200 peer">
                                    @foreach($this->brands as  $brands)
                                        <option @if($brands->status == 0) disabled @endif class="disabled:bg-[#ededea]" value="{{$brands->name}}">{{$brands->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="relative z-0 w-full mb-5 group">
                            <label for="comment" class="text-gray-400">{{ __('messages.Opmerkingen') }}
                                <div class="tooltip">
                                    <div class="tooltip-content ml-[40px]">
                                        {{ __('messages.Geef hier aan wanneer er een speciale bewerking of actie vereist is Let op: Toegevoegde bewerkingen of acties kan een meerprijs geven Neem hiervoor contact op bij vragen') }}
                                    </div>
                                    <i wire:click.prevent="" class="fa-solid fa-circle-info hover:cursor-pointer"></i>
                                </div>
                                <strong></strong>
                            </label>
                            <textarea wire:model="comment" name="comment" id="comment" class="block py-2.5 px-0 w-full text-md text-gray-900 border-0 border-b-2 border-gray-300 appearance-none dark:text-gray-900 dark:border-gray-600 focus:outline-none focus:ring-0 focus:border-b-[#C0A16E]" placeholder=" "></textarea>
                            <div class="text-red-500">@error('comment') {{ $message }} @enderror</div>
                        </div>

                        <br/><br/>
                        @foreach($offerteLines as $index => $order)
                            @php
                                $selectedOptions = $selectedPanelOption[$index] ?? [];

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

                                $currentVerticalMax = max(300, (int)($totaleLengte[$index] ?? 0) - 600);
                            @endphp

                            @if($index > 0)
                                <hr class="border-2 border-[#C0A16E]"/><br/><br/>
                            @endif

                            <div class="text-right">
                                <button wire:click.prevent="removeOfferteLine({{$index}})" type="button" class="text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2">
                                    <i class="fa-solid fa-trash hover:cursor-pointer text-white"></i>
                                </button>
                            </div>

                            <br/>

                            <div class="grid md:grid-cols-2 md:gap-6">
                                <div class="relative z-0 w-full mb-5 group">
                                    <label for="fillTotaleLengte" class="text-gray-400">{{ __('messages.Totale element lengte') }} (mm) *
                                        <div class="tooltip" wire:ignore>
                                            <div class="tooltip-content">
                                                {{ __('messages.minpanellength') }}
                                            </div>
                                            <i wire:click.prevent="" class="fa-solid fa-circle-info hover:cursor-pointer"></i>
                                        </div>
                                    </label>
                                    <input
                                        type="number"
                                        min="500"
                                        max="14500"
                                        wire:model.live.debounce.400ms="fillTotaleLengte.{{$index}}"
                                        wire:blur="normalizePanelOptions({{$index}})"
                                        name="fillTotaleLengte"
                                        id="fillTotaleLengte"
                                        class="focus:border-b-[#C0A16E] block py-2.5 px-0 w-full text-md text-gray-900 border-0 border-b-2 border-gray-300 appearance-none dark:text-gray-900 dark:border-gray-600 focus:outline-none focus:ring-0"
                                        required
                                    />
                                    <div class="text-red-500">@error('totaleLengte.'.$index) {{ $message }} @enderror</div>
                                </div>

                                <div class="relative z-0 w-full mb-5 group">
                                    <label for="aantal" class="text-gray-400">{{ __('messages.Aantal elementen') }} *
                                        <div class="tooltip">
                                            <div class="tooltip-content">
                                                {{ __('messages.Vul hier het aantal elementen in welke u nodig heeft met de ingevulde specificaties Heeft u meerdere elementen nodig met andere specificaties? Druk dan op de plus hieronder om een extra rij aan te maken') }}
                                            </div>
                                            <i wire:click.prevent="" class="fa-solid fa-circle-info hover:cursor-pointer"></i>
                                        </div>
                                    </label>
                                    <input type="number" min="1" wire:change="updateM2({{$index}})" wire:keydown="updateM2({{$index}})" wire:model="aantal.{{$index}}" name="aantal" id="aantal" class="focus:border-b-[#C0A16E] block py-2.5 px-0 w-full text-md text-gray-900 border-0 border-b-2 border-gray-300 appearance-none dark:text-gray-900 dark:border-gray-600 focus:outline-none focus:ring-0" placeholder=" " required />
                                    <div class="text-red-500">@error('aantal.'.$index) {{ $message }} @enderror</div>
                                </div>
                            </div>

                                <div class="text-right">
                                    {{ __('messages.Vierkante meters') }}: <strong>{{$this->m2[$index]}} m²</strong>
                                </div>

                                <br/><br/><br/>

                                <div wire:key="offerte-line-{{ $index }}" class="flex flex-col lg:flex-row w-full mb-[30px] gap-8 items-start">


                                    <div class="w-full lg:w-[280px] flex-shrink-0">

                                        @php
                                            $tooltips = [
                                                1 => __('messages.Meerprijs layback') . ' €' . $this->laybackPrice.',-',
                                                3 => __('messages.Meerprijs nokafschuining') . ' €' . $this->nokafschuiningPrice.',-',
                                                4 => __('messages.Meerprijs vrije ruimte') . ' €' . $this->vrijeruimtePrice.',-',
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
        {{ in_array($option, $selectedPanelOption[$index] ?? []) ? 'border-blue-500 border-2' : '' }}"
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
                                                    <label><strong>{{ __('messages.Ruimte bovenkant tot vrije ruimte') }}</strong></label>
                                                    <div class="relative">
                                                        <input type="number"
                                                               wire:model.live.debounce.400ms="panelValues.{{$index}}.4_1"
                                                               wire:blur="normalizePanelOptions({{$index}})"
                                                               wire:change="updatePanelValues({{$index}}, '4_1')"
                                                               placeholder="{{ __('messages.Vul waarde in') }}"
                                                               class="border rounded px-2 py-1 w-full mt-1">

                                                        <span class="absolute right-2 top-[77%] -translate-y-1/2 text-gray-500 text-sm pointer-events-none">
                                                            {{ __('messages.mm') }}
                                                        </span>
                                                    </div>
                                                    @error('panelValues.'.$index.'.4_1')
                                                    <div class="text-red-500 text-sm">{{ $message }}</div>
                                                    @enderror

                                                    <label><strong>{{ __('messages.Vrije ruimte') }}</strong></label>
                                                    <div class="relative">
                                                        <input type="number"
                                                               wire:model.live.debounce.400ms="panelValues.{{$index}}.4_2"
                                                               wire:blur="normalizePanelOptions({{$index}})"
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
                                                               wire:blur="normalizePanelOptions({{$index}})"
                                                               wire:change="updatePanelValues({{$index}}, {{ $option }})"
                                                               min="0"
                                                               max="60"
                                                               class="border rounded px-2 py-1 w-full pr-10 mt-1">

                                                        <span class="absolute right-2 top-[77%] -translate-y-1/2 text-gray-500 text-sm pointer-events-none">
                                                            &deg;
                                                        </span>
                                                    </div>
                                                    @error('panelValues.'.$index.'.'.$option)
                                                    <div class="text-red-500 text-sm">{{ $message }}</div>
                                                    @enderror
                                                @else
                                                    <label><strong>{{ $label }} in {{ __('messages.mm') }}</strong></label>

                                                    <select
                                                        wire:model.live.debounce.400ms="panelValues.{{$index}}.{{ $option }}"
                                                        wire:blur="normalizePanelOptions({{$index}})"
                                                        wire:change="updatePanelValues({{$index}}, {{$option}})"
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

                                                    <div   wire:key="waterstop-container-{{ $index }}-{{ $waterstopEnabled[$index] ?? 0 }}" class="border rounded p-3 mt-4 bg-gray-50">
                                                        <label class="flex items-center gap-2 font-bold cursor-pointer">
                                                            <input
                                                                type="checkbox"
                                                                @checked($waterstopEnabled[$index] ?? false)
                                                                wire:click="toggleWaterstopChecked({{$index}})"
                                                            >

                                                            {{ __('messages.Waterstop') }}

                                                            <div class="relative inline-block group">
                                                                <i wire:click.prevent="" class="fa-solid fa-circle-info text-gray-500 hover:text-blue-500 cursor-pointer"></i>

                                                                <div class="absolute left-0 top-full mt-1 w-80 bg-gray-700 text-white text-sm p-2 rounded shadow-lg
                                                    opacity-0 pointer-events-none group-hover:opacity-100 group-hover:pointer-events-auto transition-opacity duration-200 z-50">
                                                                    {{ __('messages.Meerprijs per waterstop '). ' €' . $this->waterstopPrice.',-',}}
                                                                </div>
                                                            </div>
                                                        </label>

                                                        @if($waterstopEnabled[$index] ?? false)
                                                            <div class="mt-3 space-y-3">
                                                                @foreach(($panelValues[$index]['waterstops'] ?? []) as $wsIndex => $waterstop)
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
                                                                            <strong>{{ __('messages.Positie vanaf bovenzijde') }}</strong>
                                                                        </label>

                                                                        <div class="relative">
                                                                            <input
                                                                                wire:blur="normalizePanelOptions({{$index}})"
                                                                                type="number"
                                                                                min="300"
                                                                                max="{{ $currentVerticalMax }}"
                                                                                wire:model.live="panelValues.{{$index}}.waterstops.{{$wsIndex}}.vertical"
                                                                                class="border rounded px-2 py-1 w-full mt-1 pr-10"
                                                                                placeholder="300"
                                                                            >

                                                                            <span class="absolute right-2 top-[58%] -translate-y-1/2 text-gray-500 text-sm pointer-events-none">
                                                                {{ __('messages.mm') }}
                                                            </span>
                                                                        </div>

                                                                        <div class="text-xs text-gray-500 mt-1">
                                                                            {{ __('messages.Minimaal 300 mm vanaf bovenzijde') }}
                                                                            @if(($totaleLengte[$index] ?? 0) > 0)
                                                                                <br>{{ __('messages.Maximaal') }}: {{ $currentVerticalMax }} {{ __('messages.mm') }}
                                                                            @endif
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
                                                        @endif
                                                    </div>
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
                                                :panel-values="$panelValues[$index] ?? []"
                                                :totale-lengte="$totaleLengte[$index] ?? 0"
                                            />
                                        </div>
                                    </div>


                                </div>
                        @endforeach

                        <div class="text-right">
                            <button wire:click="addOfferteLine()" type="button" class="text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-gray-700 dark:border-gray-700">
                                <i class="fa fa-plus hover:cursor-pointer"></i>{{ __('messages.Element toevoegen') }}
                            </button>
                        </div>

                        <div
                            x-data="{ show:false, message:'' }"
                            x-on:show-form-error.window="
        message = $event.detail.message;
        show = true;
        setTimeout(() => show = false, 5000);
    "
                        >
                            <div
                                x-show="show"
                                x-transition
                                class="fixed top-5 right-5 z-50 bg-red-500 text-white px-5 py-3 rounded shadow-lg"
                            >
                                <i class="fa-solid fa-circle-exclamation mr-2"></i>
                                <span x-text="message"></span>
                            </div>
                        </div>

                        <button
                            wire:click.prevent="saveOfferte"
                            @disabled($isSaving || !count($this->offerteLines))
                            class="text-white bg-[#C0A16E] mt-10 hover:bg-[#d1b079] disabled:bg-[#c0a16e99] disabled:cursor-not-allowed hover:cursor-pointer focus:outline-none font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center"
                        >
                            @if($isSaving)
                                <i class="fa-solid fa-spinner fa-spin"></i>
                                {{ __('messages.Offerte plaatsen') }}
                            @else
                                {{ __('messages.Offerte plaatsen') }}
                            @endif
                        </button>


                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    window.addEventListener('show-form-error', () => {
        setTimeout(() => {
            let error = document.querySelector('.text-red-500');

            if(error){
                error.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            }
        }, 100);
    });
</script>
<script>
    window.addEventListener('capture-panel-renders', async () => {

        let elements = document.querySelectorAll('[id^="panel-render-"]');

        for (let index = 0; index < elements.length; index++) {

            let element = elements[index];

            const rect = element.getBoundingClientRect();

            let canvas = await html2canvas(element, {
                scale: 2,
                backgroundColor: '#ffffff',

                x: 0,
                y: 0,
                width: rect.width,
                height: rect.height,

                onclone: (clonedDoc) => {

                    clonedDoc.querySelectorAll('*').forEach(el => {

                        const style = window.getComputedStyle(el);

                        if (style.color.includes('oklch')) {
                            el.style.color = '#000000';
                        }

                        if (style.backgroundColor.includes('oklch')) {
                            el.style.backgroundColor = '#ffffff';
                        }

                        if (style.borderColor.includes('oklch')) {
                            el.style.borderColor = '#000000';
                        }

                    });

                }
            });


            // Witruimte automatisch verwijderen
            const croppedCanvas = cropCanvas(canvas);

            let image = croppedCanvas.toDataURL('image/png');


            await Livewire.dispatch('save-panel-render', {
                index:index,
                image:image
            });
        }

    });


    // Snijdt transparante/witte randen weg
    function cropCanvas(canvas) {

        let ctx = canvas.getContext('2d');
        let pixels = ctx.getImageData(
            0,
            0,
            canvas.width,
            canvas.height
        );

        let data = pixels.data;

        let minX = canvas.width;
        let minY = canvas.height;
        let maxX = 0;
        let maxY = 0;


        for(let y = 0; y < canvas.height; y++) {

            for(let x = 0; x < canvas.width; x++) {

                let i = (y * canvas.width + x) * 4;

                let r = data[i];
                let g = data[i+1];
                let b = data[i+2];
                let a = data[i+3];


                // Alles wat niet wit is telt als inhoud
                if(
                    a > 0 &&
                    !(r > 245 && g > 245 && b > 245)
                ) {

                    if(x < minX) minX = x;
                    if(y < minY) minY = y;
                    if(x > maxX) maxX = x;
                    if(y > maxY) maxY = y;

                }
            }
        }


        // niks gevonden
        if(maxX === 0) {
            return canvas;
        }


        let width = maxX - minX;
        let height = maxY - minY;


        let newCanvas = document.createElement('canvas');

        newCanvas.width = width;
        newCanvas.height = height;


        newCanvas
            .getContext('2d')
            .drawImage(
                canvas,
                minX,
                minY,
                width,
                height,
                0,
                0,
                width,
                height
            );


        return newCanvas;
    }
</script>
