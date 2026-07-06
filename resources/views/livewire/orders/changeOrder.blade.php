
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
                    <a href="/orders" class="inline-flex items-center md:ms-2 text-sm font-medium text-gray-700 hover:text-[#C0A16E] ">
                        {{ __('messages.Mijn orders') }}
                    </a>
                </div>
            </li>

            <li>
                <div class="flex items-center">
                    <i class="fa-solid fa-angle-right"></i>
                    <p class="ms-1 text-sm font-medium text-gray-700 md:ms-2">   {{ __('messages.Order bewerken') }}</p>
                </div>
            </li>
        </ol>
    </nav>
</x-slot>
<div class="py-12">
    <div class="max-w-9xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="grid">
                    @if($showPriceUpdateModal)
                        <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-[9999]">
                            <div class="border-2 border-[#C0A16E] bg-white rounded-lg shadow-lg p-6 max-w-md w-full text-center">

                                <h3 class="text-lg font-bold mb-4">
                                    {{ __('messages.Prijswijziging gedetecteerd') }}
                                </h3>

                                <p class="mb-6">
                                    {{ $priceUpdateMessage }}
                                </p>

                                <div class="flex justify-center gap-4">
                                    <button
                                        type="button"
                                        wire:click="keepOldOrderPrices"
                                        class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400"
                                    >
                                        {{ __('messages.Oude prijzen houden') }}
                                    </button>

                                    <button
                                        type="button"
                                        wire:click="useNewOrderPrices"
                                        class="px-4 py-2 bg-[#C0A16E] text-white rounded hover:bg-[#d1b079]"
                                    >
                                        {{ __('messages.Nieuwe prijzen toepassen') }}
                                    </button>
                                </div>

                            </div>
                        </div>
                    @endif

                    <div x-data="{ showConfirmModal: @entangle('showConfirmModal') }">
                        <!-- Je content / form hier -->

                        <!-- Modal -->
                        <div x-show="showConfirmModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50" style="display: none;">
                            <div class="border-2 border-[#C0A16E] bg-white rounded-lg shadow-lg p-6 max-w-md w-full text-center">
                                <h3 class="text-lg font-bold mb-4">{{ __('Let op!') }}</h3>
                                <p class="mb-6">

                                    De inkooporder is al verstuurd naar de leverancier. Neemt eerst contact op met de leverancier om te kijken of deze de inkooporder al heeft verwerkt en voorkom verkeerde leveringen. <br/><br/><Strong>Prijsregels</strong><br/>De prijsregels die bij het bevestigen zijn toegevoegd worden automatisch verwijderd als je de order toch bewerkt. Deze moet je later weer handmatig toevoegen.</p>
                                <div class="flex justify-center gap-4">
                                    <button @click="showConfirmModal = false" wire:click="cancelChangeOrder()"  class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
                                        Annuleren
                                    </button>
                                    <button @click="showConfirmModal = false" class="px-4 py-2 bg-[#C0A16E] text-white rounded hover:bg-[#d1b079]">
                                        Toch bewerken
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <form>
                        <div class="relative">
                            <i wire:click="cancelChangeOrder()" class="absolute right-0 fa-solid fa-xmark text-xl hover:cursor-pointer"></i>
                        </div>

                        {{ __('messages.Project gegevens') }}
                        <br/><br/>
                        <div class="grid md:grid-cols-2 md:gap-6">
                            <div class="relative z-0 w-full mb-5 group">
                                <label for="klant_naam" class="text-gray-400">{{ __('messages.Klantnaam') }} *</label>
                                <input type="text" wire:model="klant_naam" name="klant_naam" id="klant_naam" class="block py-2.5 px-0 w-full text-md text-gray-900 border-0 border-b-2 border-gray-300 appearance-none dark:text-gray-900 dark:border-gray-600 focus:outline-none focus:ring-0 focus:border-b-[#C0A16E]" placeholder=" " required />
                                <div class="text-red-500">@error('klant_naam') {{ $message }} @enderror</div>
                            </div>

                            <div class="relative z-0 w-full mb-5 group">
                                <label for="requested_delivery_date" class="text-gray-400">{{ __('messages.Gewenste leverdatum') }} *</label>

                                <input
                                    type="text"
                                    class="datepicker block w-full bg-neutral-secondary-medium border-0 border-b-2 border-gray-300 appearance-none dark:text-gray-900 dark:border-gray-600 focus:outline-none focus:ring-0 focus:border-b-[#C0A16E]"
                                    wire:model="requested_delivery_date"
                                    placeholder="{{ __('messages.Selecteer datum') }}"
                                />
                                <div class="text-red-500">@error('requested_delivery_date') {{ $message }} @enderror</div>
                            </div>
                        </div>

                        <div class="grid md:grid-cols-2 md:gap-6">
                            <div class="relative z-0 w-full mb-5 group">
                                <label for="project_naam" class="text-gray-400">{{ __('messages.Projectnaam') }} *</label>
                                <input type="text" wire:model="project_naam" name="project_naam" id="project_naam" class="block py-2.5 px-0 w-full text-md text-gray-900 border-0 border-b-2 border-gray-300 appearance-none dark:text-gray-900 dark:border-gray-600 focus:outline-none focus:ring-0 focus:border-b-[#C0A16E]" placeholder=" " required />
                                <div class="text-red-500">@error('project_naam') {{ $message }} @enderror</div>
                            </div>
                            <div class="relative z-0 w-full mb-5 group">
                                <label for="referentie" class="text-gray-400">{{ __('messages.Referentie') }} *</label>
                                <input type="text" wire:model="referentie" name="referentie" id="referentie" class="block py-2.5 px-0 w-full text-md text-gray-900 border-0 border-b-2 border-gray-300 appearance-none dark:text-gray-900 dark:border-gray-600 focus:outline-none focus:ring-0 focus:border-b-[#C0A16E]" placeholder=" " required />
                                <div class="text-red-500">@error('referentie') {{ $message }} @enderror</div>
                            </div>
                        </div>

                        <div class="grid md:grid-cols-2 md:gap-6">
                            <div class="relative z-0 w-full mb-5 group">
                                <label for="aflever_straat" class="text-gray-400">{{ __('messages.Aflever straat') }} *</label>
                                <input type="text" wire:model="aflever_straat" name="aflever_straat" id="aflever_straat" class="block py-2.5 px-0 w-full text-md text-gray-900 border-0 border-b-2 border-gray-300 appearance-none dark:text-gray-900 dark:border-gray-600 focus:outline-none focus:ring-0 focus:border-b-[#C0A16E]" placeholder=" " />
                                <div class="text-red-500">@error('aflever_straat') {{ $message }} @enderror</div>
                            </div>
                            <div class="relative z-0 w-full mb-5 group">
                                <label for="aflever_postcode" class="text-gray-400">{{ __('messages.Aflever postcode') }} *</label>
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
                                <label for="intaker" class="text-gray-400">{{ __('messages.Verkoper') }} *</label>
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
                                <label for="merk_paneel" class="text-gray-400">{{ __('messages.Element') }} *</label>
                                <select @if(count($this->orderLines)) disabled @endif id="merk_paneel" wire:model="merk_paneel" class="disabled:hover:cursor-not-allowed block py-2.5 px-0 w-full text-sm text-gray-500 bg-transparent border-0 border-b-2 border-gray-200 appearance-none dark:text-gray-900 dark:border-gray-700 focus:outline-none focus:ring-0 focus:border-gray-200 peer">
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
                        @foreach($orderLines as $index => $order)
                            @php
                                $selectedOptions = $selectedPanelOption[$index] ?? [];

                                $waterstopTypes = [
                                    960 => 't.b.v. Velux MK 04,06,08,10,12',
                                    840 => 't.b.v. Velux FK 04,06',
                                    730 => 't.b.v. Velux CK 02,04',
                                    500 => 't.b.v. Ubbink doorvoeren',
                                    300 => 't.b.v. Kleine doorvoeren en/of overgangen horizontaal naar verticaal bij dakkappellen',
                                ];

                                $waterstopHorizontalMax = [
                                    960 => 0,
                                    840 => 60,
                                    730 => 115,
                                    500 => 230,
                                    300 => 330,
                                ];

                                $selectedWaterstopType = (int)($panelValues[$index]['waterstop_type'] ?? 0);
                                $currentHorizontalMax = $waterstopHorizontalMax[$selectedWaterstopType] ?? 0;
                                $currentVerticalMax = max(300, (int)($totaleLengte[$index] ?? 0) - 600);
                            @endphp

                            @if($index > 0)
                                <hr class="border-2 border-[#C0A16E]"/><br/><br/>
                            @endif
                            <div class="text-right">
                                <button wire:click.prevent="removeOrderLine({{$index}})" type="button" class="text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2">
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
                                    <input type="number" value="" min="500" max="14500" wire:model="fillTotaleLengte.{{$index}}" wire:change="updateTotaleLengte({{$index}})" wire:keydown="updateTotaleLengte({{$index}})" name="fillTotaleLengte" id="fillTotaleLengte" class="focus:border-b-[#C0A16E] block py-2.5 px-0 w-full text-md text-gray-900 border-0 border-b-2 border-gray-300 appearance-none dark:text-gray-900 dark:border-gray-600 focus:outline-none focus:ring-0" required />
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
                                    <input type="number" min="1" value="" wire:change="updateM2({{$index}})" wire:keydown="updateM2({{$index}})" wire:model="aantal.{{$index}}" name="aantal" id="aantal" class="focus:border-b-[#C0A16E] block py-2.5 px-0 w-full text-md text-gray-900 border-0 border-b-2 border-gray-300 appearance-none dark:text-gray-900 dark:border-gray-600 focus:outline-none focus:ring-0" placeholder=" " required />

                                    <div class="text-red-500">@error('aantal.'.$index) {{ $message }} @enderror</div>
                                </div>
                            </div>

                            <div class="text-right">
                                {{ __('messages.Vierkante meters') }}: <strong>{{$this->m2[$index]}} m²</strong>
                            </div>
                            <br/><br/><br/>

                            <div class="flex flex-col sm:flex-row w-full mb-[30px] gap-2">
                                <div class="w-full sm:w-[250px] flex flex-col gap-2">

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
                                            <input type="checkbox"
                                                   wire:model="selectedPanelOption.{{$index}}"
                                                   wire:click="updateSelectedPanelOption({{$index}})"
                                                   value="{{ $option }}"
                                                   class="hidden peer">

                                            <div class="border rounded p-1 w-full peer-checked:border-blue-500 relative">
                                                <img src="{{ asset("storage/images/rietpanel/paneel-$option.png") }}" class="w-full h-[50px] object-contain">
                                                <div class="text-center font-bold mt-1">{{ $label }}</div>

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

                                            @if(in_array($option, $selectedOptions))
                                                @if($option == 4)
                                                    <label><strong>{{ __('messages.Ruimte bovenkant tot vrije ruimte') }}</strong></label>
                                                    <div class="relative">
                                                        <input type="number"
                                                               wire:model="panelValues.{{$index}}.4_1"
                                                               wire:keydown="updatePanelValues({{$index}}, '4_1')"
                                                               placeholder="Vul waarde in"
                                                               class="border rounded px-2 py-1 w-full mt-1">

                                                        <span class="absolute right-2 top-[77%] -translate-y-1/2 text-gray-500 text-sm">
                                                            mm
                                                        </span>
                                                    </div>

                                                    @error('panelValues.'.$index.'.4_1')
                                                    <div class="text-red-500 text-sm">{{ $message }}</div>
                                                    @enderror
                                                    <label><strong>{{ __('messages.Vrije ruimte') }}</strong></label>
                                                    <div class="relative">
                                                        <input type="number"
                                                               wire:model="panelValues.{{$index}}.4_2"
                                                               wire:keydown="updatePanelValues({{$index}}, '4_2')"
                                                               placeholder="Vul waarde in"
                                                               class="border rounded px-2 py-1 w-full mt-1">
                                                        <span class="absolute right-2 top-[77%] -translate-y-1/2 text-gray-500 text-sm">
                                                            mm
                                                        </span>
                                                    </div>
                                                    <div class="text-red-500 text-sm mt-1">
                                                        @error('panelValues.'.$index.'.4_2') {{ $message }} @enderror
                                                    </div>
                                                @elseif($option == 3)
                                                    <label><strong>{{ $label }} in graden</strong></label>
                                                    <div class="relative">
                                                        <input type="number"
                                                               wire:model="panelValues.{{$index}}.{{ $option }}"
                                                               wire:keydown="updatePanelValues({{$index}}, {{$option}})"
                                                               min="0"
                                                               max="60"
                                                               class="border rounded px-2 py-1 w-full pr-10 mt-1">

                                                        <span class="absolute right-2 top-[77%] -translate-y-1/2 text-gray-500 text-sm">
                                                            &deg;
                                                        </span>
                                                    </div>
                                                    @error('panelValues.'.$index.'.'.$option)
                                                    <div class="text-red-500 text-sm">{{ $message }}</div>
                                                    @enderror
                                                @else
                                                    <label><strong>{{ $label }} in mm</strong></label>

                                                    <select
                                                        wire:model="panelValues.{{$index}}.{{ $option }}"
                                                        wire:change="updatePanelValues({{$index}}, {{$option}})"
                                                        class="border rounded px-2 py-1 w-full mt-1"
                                                    >
                                                        @for($i = 20; $i <= 140; $i += 20)
                                                            <option value="{{ $i }}">{{ $i }} mm</option>
                                                        @endfor
                                                    </select>

                                                    <div class="text-red-500 text-sm mt-1">
                                                        @error('panelValues.'.$index.'.'.$option) {{ $message }} @enderror
                                                    </div>
                                                @endif
                                            @endif
                                        </label>
                                    @endforeach

                                    <div class="border rounded p-3 mt-4 bg-gray-50">
                                        <label class="flex items-center gap-2 font-bold cursor-pointer">
                                            <input
                                                type="checkbox"
                                                @checked($waterstopEnabled[$index] ?? false)
                                                wire:click="toggleWaterstop({{$index}})"
                                            >

                                            {{ __('messages.Waterstop') }}

                                            <div class="relative inline-block group">
                                                <i wire:click.prevent="" class="fa-solid fa-circle-info text-gray-500 hover:text-blue-500 cursor-pointer"></i>

                                                <div class="absolute left-0 top-full mt-1 w-80 bg-gray-700 text-white text-sm p-2 rounded shadow-lg
                opacity-0 pointer-events-none group-hover:opacity-100 group-hover:pointer-events-auto transition-opacity duration-200 z-50">
                                                    {{ __('messages.Waterstop wordt niet meegenomen in de render Alleen het type en de positie worden opgeslagen') }}
                                                </div>
                                            </div>
                                        </label>

                                        @if($waterstopEnabled[$index] ?? false)
                                            @php
                                                $waterstopOptions = [
                                                    960 => __('messages.waterstop_960'),
                                                    840 => __('messages.waterstop_840'),
                                                    730 => __('messages.waterstop_730'),
                                                    500 => __('messages.waterstop_500'),
                                                    300 => __('messages.waterstop_300'),
                                                ];
                                            @endphp

                                            <label class="block mt-3">
                                                <strong>{{ __('messages.Waterstop type') }}</strong>
                                            </label>

                                            <div class="mt-1">
                                                <select
                                                    wire:model.live="panelValues.{{$index}}.waterstop_type"
                                                    class="border rounded px-2 py-1 w-full"
                                                >
                                                    <option value="">{{ __('messages.Selecteer') }}...</option>

                                                    @foreach($waterstopOptions as $value => $text)
                                                        <option value="{{ $value }}">
                                                            {{ $value }} {{ __('messages.mm') }}
                                                        </option>
                                                    @endforeach
                                                </select>

                                                @if(!empty($panelValues[$index]['waterstop_type']))
                                                    <div class="mt-2 rounded-md bg-gray-100 border border-gray-200 p-2 text-sm text-gray-600">
                                                        <i class="fa-solid fa-circle-info mr-1 text-gray-500"></i>
                                                        {{ $waterstopOptions[$panelValues[$index]['waterstop_type']] }}
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="text-red-500 text-sm mt-1">
                                                @error('panelValues.'.$index.'.waterstop_type') {{ $message }} @enderror
                                            </div>

                                            <label class="block mt-3">
                                                <strong>{{ __('messages.Positie vanaf bovenzijde') }}</strong>
                                            </label>

                                            <div class="relative">
                                                <input
                                                    type="number"
                                                    min="300"
                                                    max="{{ $currentVerticalMax }}"
                                                    wire:model="panelValues.{{$index}}.waterstop_vertical"
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
                                                @error('panelValues.'.$index.'.waterstop_vertical') {{ $message }} @enderror
                                            </div>

                                            <label class="block mt-3">
                                                <strong>{{ __('messages.Horizontale verplaatsing vanuit midden') }}</strong>
                                            </label>

                                            <div class="relative">
                                                <input
                                                    type="number"
                                                    wire:model.live="panelValues.{{$index}}.waterstop_horizontal"
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
                                                @if(!empty($panelValues[$index]['waterstop_type']))
                                                    <br>{{ __('messages.Maximaal') }}: {{ $currentHorizontalMax }} {{ __('messages.mm') }} {{ __('messages.naar links en rechts') }}.
                                                @else
                                                    <br>{{ __('messages.Selecteer eerst een type waterstop') }}.
                                                @endif
                                            </div>

                                            <div class="text-red-500 text-sm mt-1">
                                                @error('panelValues.'.$index.'.waterstop_horizontal') {{ $message }} @enderror
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex-1 flex justify-center mt-[40px]">
                                    <div class="relative md:w-[90%] mx-auto">
                                        <img src="{{ asset($panelImages[$index] ?? 'storage/images/rietpanel/paneel.png') }}"
                                             class="w-full h-auto block"
                                             alt="Panel"
                                        />

                                        <div class="absolute top-[-40px] left-[57%] transform -translate-x-1/2 text-[12px] font-bold 2xl:text-[14px] lg:top-[-50px] ">
                                            {{ __('messages.Totale maat') }}: @if($totaleLengte[$index]) < {{$totaleLengte[$index]}}  mm > @else  < 0 mm > @endif
                                        </div>

                                        @if(in_array(1, $selectedOptions))
                                            <div class="absolute 2xl:text-[12px] @if(in_array(2, $selectedOptions)) md:left-[12%]  top-[-11px] lg:left-[13%]  2xl:left-[13.3%] left-[11.5%] text-[8px] font-bold  lg:top-[7px] @else md:left-[13%] top-[-11px] left-[11.5%] text-[8px]  lg:left-[14.7%] font-bold  lg:top-[7px]@endif ">
                                                {{ $this->panelValues[$index]['1'].' mm' ?? '0 mm' }}
                                            </div>
                                        @endif

                                        @if(in_array(2, $selectedOptions))
                                            <div class="absolute top-[50px] right-[-2px] mr-2 mb-2 text-[8px] font-bold
                                                sm:top-[50px] md:top-[60px] md:right-[0px]
                                                lg:right-[10px] lg:top-[115px]
                                                xl:top-[110px]
                                                2xl:text-[12px]
                                                {{ (!in_array(1, $selectedOptions) && !in_array(4, $selectedOptions))
                                                    ? '2xl:top-[155px] 2xl:right-[40px]'
                                                    : '2xl:top-[210px] 2xl:right-[25px]' }}
                                            ">
                                                {{ $this->panelValues[$index]['2'].' mm' ?? '0 mm' }}
                                            </div>
                                        @endif

                                        @if(in_array(4, $selectedOptions))
                                            <div class="absolute 2xl:text-[12px]  @if(in_array(2, $selectedOptions)) top-[-11px]  left-[23%] text-[8px] font-bold md:left-[25%] lg:left-[28%] xl:left-[25%]  lg:top-[7px] @else  lg:top-[7px] xl:left-[29%] top-[-11px]  left-[24%] text-[8px] font-bold md:left-[26%] lg:left-[29%] @endif ">
                                                {{ $this->panelValues[$index]['4_1'].' mm ' ?? ' 0 mm ' }}
                                            </div>
                                            <div class="absolute 2xl:text-[12px] top-[-11px] @if(in_array(2, $selectedOptions)) xl:left-[47%]  left-[43%] text-[8px] font-bold md:left-[45%] lg:left-[47%]  lg:top-[7px] @else  lg:top-[7px] xl:left-[52%] lg:top-[15px]   left-[51%] text-[8px] font-bold md:left-[50%] lg:left-[51%] @endif  ">
                                                {{ $this->panelValues[$index]['4_2'].' mm ' ?? '0 mm ' }}
                                            </div>
                                        @endif

                                        @if(in_array(3, $selectedOptions))
                                            <div class="absolute 2xl:text-[12px]
                                                @if(in_array(2, $selectedOptions))
                                                    {{ (!in_array(1, $selectedOptions) && !in_array(4, $selectedOptions)) ? '2xl:top-[200px]' : '2xl:top-[245px]' }}
                                                    top-[55px] left-[5px] mr-2 mb-2 text-[8px] font-bold
                                                    sm:top-[50px] md:top-[70px] lg:top-[130px] lg:left-[7px] 2xl:left-[30px] xl:top-[135px]
                                                @else
                                                    {{ (!in_array(1, $selectedOptions) && !in_array(4, $selectedOptions)) ? '2xl:top-[200px]' : '2xl:top-[265px]' }}
                                                    top-[62px] left-[5px] mr-2 mb-2 text-[8px] font-bold
                                                    sm:top-[50px] md:top-[82px] lg:top-[150px] xl:top-[135px] lg:left-[7px] 2xl:left-[30px]
                                                @endif
                                            ">
                                                {{ $this->panelValues[$index]['3'] ?? 0 }} &deg;
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        <div class="text-right">

                            <button wire:click="addOrderLine()" type="button" class="text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-gray-700 dark:border-gray-700">
                                <i class="fa fa-plus hover:cursor-pointer"></i>{{ __('messages.Element toevoegen') }}
                            </button>

                        </div>

                        <button wire:loading.attr="disabled" wire:target="saveOrder" wire:click.prevent="saveOrder()" @if(!count($this->orderLines)) disabled @endif class="text-white bg-[#C0A16E] mt-10 hover:bg-[#d1b079] disabled:bg-[#c0a16e99] disabled:cursor-not-allowed hover:cursor-pointer focus:outline-none font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center">
                            <div wire:loading wire:target="saveOrder">
                                <i class="fa-solid fa-spinner fa-spin"></i>{{ __('messages.Order updaten') }}
                            </div>
                            <div wire:loading.attr="hidden" wire:target="saveOrder">
                                {{ __('messages.Order updaten') }}
                            </div>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
