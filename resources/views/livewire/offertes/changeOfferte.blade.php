
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
                    <p class="ms-1 text-sm font-medium text-gray-700 md:ms-2">   {{ __('messages.Offerte bewerken') }}</p>
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
                    <form>
                        <div class="relative">
                            <i wire:click="cancelChangeOfferte()" class="absolute right-0 fa-solid fa-xmark text-xl hover:cursor-pointer"></i>
                        </div>

                        {{ __('messages.Project gegevens') }}
                        <br/><br/>
                        <div class="grid md:grid-cols-2 md:gap-6">
                            <div class="relative z-0 w-full mb-5 group">
                                <label for="klant_naam" class="text-gray-400">   {{ __('messages.Klant naam') }} *</label>
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
                                <label for="klant_naam" class="text-gray-400">   {{ __('messages.Project naam') }} *</label>
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

                            @user
                            @if($this->company->is_reseller == 0)
                                <div class="relative z-0 w-full mb-5 group">
                                    <label for="marge" class="text-gray-400"><strong>{{ __('messages.Jouw prijs') }}:    @if($this->priceRulePrice == 0)({{ __('messages.Selecteer kerndikte') }}) @else <?php $discount = $this->companyDiscount /100 * $this->priceRulePrice?>
                                            {!! '&euro;&nbsp;' . number_format($this->priceRulePrice - $discount, 2, ',', '.') !!} excl. BTW excl. marge @endif
                                        </strong></label><br/>
                                    @admin<label><small>Je ziet de marge omdat jouw bedrijf geen reseller is maar een gewoon bedrijf en je ingelogd bent als admin<br/></small></label>@endadmin
                                    <label for="marge" class="text-gray-400">Marge (%)</label>
                                    <div class="tooltip">
                                        <div class="tooltip-content">
                                            {{ __('messages.percentage_marge') }}

                                        </div>
                                        <i wire:click.prevent="" class="fa-solid fa-circle-info hover:cursor-pointer" id="tooltip-marge"></i>
                                    </div> *
                                    <input type="number" wire:model="marge" name="marge" id="marge" class="block py-2.5 px-0 w-full text-md text-gray-900 border-0 border-b-2 border-gray-300 appearance-none dark:text-gray-900 dark:border-gray-600 focus:outline-none focus:ring-0 focus:border-b-[#C0A16E]" placeholder=" " required />
                                    <div class="text-red-500">@error('marge') {{ $message }} @enderror</div>
                                </div>
                            @endif
                            @enduser
                        </div>
                        <div class="grid md:grid-cols-2 md:gap-6">
                            <div class="relative z-0 w-full mb-5 group">
                                <label for="toepassing" class="text-gray-400">{{ __('messages.Toepassing') }} *</label>
                                <select id="toepassing" wire:model="toepassing" wire:change="updateBrands()" class="block py-2.5 px-0 w-full text-sm text-gray-500 bg-transparent border-0 border-b-2 border-gray-200 appearance-none dark:text-gray-900 dark:border-gray-700 focus:outline-none focus:ring-0 focus:border-gray-200 peer">

                                    <option value="Dak">{{ __('messages.Dak') }}</option>
                                    <option value="Wand">{{ __('messages.Wand') }}</option>

                                </select>
                            </div>


                            <div class="relative z-0 w-full mb-5 group">
                                <label for="merk_paneel" class="text-gray-400">{{ __('messages.Merk paneel') }} *</label>
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
                        @foreach($offerteLines as $index => $offerte)
                            @if($index > 0)
                                <hr class="border-2 border-[#C0A16E]"/><br/><br/>
                            @endif
                            <div class="text-right">
                                <button wire:click.prevent="removeOfferteLine({{$index}})" type="button" class="text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2">
                                    <i class="fa-solid fa-trash hover:cursor-pointer text-white"></i>
                                </button>


                            </div>
                            <br/>
                            {{ __('messages.Afmetingen paneel') }}<br/><br/>
                            {{--                            <div class="grid md:grid-cols-2 md:gap-6">--}}
                            {{--                                <div class="relative z-0 w-full mb-5 group">--}}
                            {{--                                    <label for="fillLb" class="text-gray-400">LB (max 210mm)--}}
                            {{--                                        <div class="tooltip">--}}
                            {{--                                            <div class="tooltip-content">--}}
                            {{--                                                Vul hier de LB in mm in. De maximale LB mag 210mm zijn. Laat dit op nul staan als de LB niet van toepassing is. Heeft u toch een grotere LB nodig? Neem dan contact met ons op.--}}
                            {{--                                            </div>--}}
                            {{--                                            <i wire:click.prevent="" class="fa-solid fa-circle-info hover:cursor-pointer" id="tooltip{{$index}}"></i>--}}
                            {{--                                        </div>--}}
                            {{--                                    </label>--}}
                            {{--                                    <input type="number" min="0" max="210" wire:model="fillLb.{{$index}}" wire:keydown="updateLb({{$index}})" name="fillLb" id="fillLb" class="focus:border-b-[#C0A16E] block py-2.5 px-0 w-full text-md text-gray-900 border-0 border-b-2 border-gray-300 appearance-none dark:text-gray-900 dark:border-gray-600 focus:outline-none focus:ring-0" placeholder=" " required />--}}
                            {{--                                    <div class="text-red-500">@error('lb.'.$index) {{ $message }} @enderror</div>--}}
                            {{--                                </div>--}}
                            {{--                                <div class="relative z-0 w-full mb-5 group">--}}
                            {{--                                    <label for="fillLb" class="text-gray-400">CB (max 200mm)--}}
                            {{--                                        <div class="tooltip">--}}
                            {{--                                            <div class="tooltip-content">--}}
                            {{--                                               Vul hier de CB in mm in. De maximale CB mag 200mm zijn. Laat dit op nul staan als de CB niet van toepassing is. Heeft u toch een grotere CB nodig? Neem dan contact met ons op.--}}
                            {{--                                            </div>--}}
                            {{--                                            <i wire:click.prevent="" class="fa-solid fa-circle-info hover:cursor-pointer"></i>--}}
                            {{--                                        </div>--}}
                            {{--                                    </label>--}}
                            {{--                                    <input type="number" min="0" max="200" wire:model="fillCb.{{$index}}" wire:keydown="updateCb({{$index}})" name="fillCb" id="fillCb" class="focus:border-b-[#C0A16E] block py-2.5 px-0 w-full text-md text-gray-900 border-0 border-b-2 border-gray-300 appearance-none dark:text-gray-900 dark:border-gray-600 focus:outline-none focus:ring-0" placeholder=" " required />--}}
                            {{--                                    <div class="text-red-500">@error('cb.'.$index) {{ $message }} @enderror</div>--}}
                            {{--                                </div>--}}
                            {{--                            </div>--}}



                            <div class="grid md:grid-cols-2 md:gap-6">
                                <div class="relative z-0 w-full mb-5 group">
                                    <label for="fillTotaleLengte" class="text-gray-400"> {{ __('messages.Totale paneellengte') }} (mm) *
                                        <div class="tooltip" wire:ignore>
                                            <div class="tooltip-content">
                                                {{ __('messages.Vul hier de totale paneel lengte in mm in, inclusief de CB in De minimale lengte moet 500mm zijn en de maximale lengte mag 14500mm zijn Wil je langere lengtes bestellen? Neem dan contact met ons op') }}
                                            </div>
                                            <i wire:click.prevent="" class="fa-solid fa-circle-info hover:cursor-pointer"></i>
                                        </div>
                                    </label>
                                    <input type="number" value="" min="500" max="14500" wire:model="fillTotaleLengte.{{$index}}" wire:change="updateTotaleLengte({{$index}})" wire:keydown="updateTotaleLengte({{$index}})" name="fillTotaleLengte" id="fillTotaleLengte" class="focus:border-b-[#C0A16E] block py-2.5 px-0 w-full text-md text-gray-900 border-0 border-b-2 border-gray-300 appearance-none dark:text-gray-900 dark:border-gray-600 focus:outline-none focus:ring-0" required />
                                    <div class="text-red-500">@error('fillTotaleLengte.'.$index) {{ $message }} @enderror</div>
                                </div>
                                <div class="relative z-0 w-full mb-5 group">
                                    <label for="fillTotaleLengte" class="text-gray-400">{{ __('messages.Aantal panelen') }} *
                                        <div class="tooltip">
                                            <div class="tooltip-content">
                                                {{ __('messages.Vul hier het aantal panelen in welke u nodig heeft met de ingevulde specificaties Heeft u meerdere panelen nodig met andere specificaties? Druk dan op de plus hieronder om een extra rij aan te maken') }}
                                            </div>
                                            <i wire:click.prevent="" class="fa-solid fa-circle-info hover:cursor-pointer"></i>
                                        </div>
                                    </label>
                                    <input type="number" min="1" value="" wire:change="updateM2({{$index}})" wire:keydown="updateM2({{$index}})" wire:model="aantal.{{$index}}"  name="aantal" id="aantal" class="focus:border-b-[#C0A16E] block py-2.5 px-0 w-full text-md text-gray-900 border-0 border-b-2 border-gray-300 appearance-none dark:text-gray-900 dark:border-gray-600 focus:outline-none focus:ring-0" placeholder=" " required />

                                    <div class="text-red-500">@error('aantal.'.$index) {{ $message }} @enderror</div>
                                </div>
                            </div>


                                <div class="text-right">
                                    {{ __('messages.Vierkante meters') }}: <strong>{{$this->m2[$index]}} m²</strong>
                                </div>
                                <br/><br/><br/>
                                <div class="flex flex-col sm:flex-row w-full mb-[30px] gap-2">

                                    <!-- Linker div (checkboxes) -->
                                    <div class="w-full sm:w-[250px] flex flex-col gap-2">

                                        @php
                                            $tooltips = [
                                                 1 =>  __('messages.Meerprijs layback') . ' €' . $this->laybackPrice.',-',
                                                 3 => __('messages.Meerprijs nokafschuining') . ' €' . $this->nokafschuiningPrice.',-',
                                                 4 =>  __('messages.Meerprijs vrije ruimte') . ' €' . $this->vrijeruimtePrice.',-',
                                            ];
                                        @endphp

                                        @foreach([
                                            1 => __('messages.Layback'),
                                            2 => __('messages.Cutback'),
                                            3 => __('messages.Nok afschuining'),
                                            4 => __('messages.Vrije ruimte')
                                        ] as $option => $label)
                                            <label class="cursor-pointer flex flex-col relative mt-[20px]">
                                                <!-- Checkbox -->
                                                <input type="checkbox"
                                                       wire:model="selectedPanelOption.{{$index}}"
                                                       wire:click="updateSelectedPanelOption({{$index}})"
                                                       value="{{ $option }}"
                                                       class="hidden peer">

                                                <!-- Afbeelding + label -->
                                                <div class="border rounded p-1 w-full peer-checked:border-blue-500 relative">
                                                    <img src="{{ asset("storage/images/rietpanel/paneel-$option.png") }}" class="w-full h-[50px] object-contain">

                                                    <div class="text-center font-bold mt-1">{{ $label }}</div>
                                                    @if(isset($tooltips[$option]))
                                                        <!-- Tooltip rechtsboven -->
                                                        <div class="absolute top-1 right-1">
                                                            <!-- Wrapper met group -->
                                                            <div class="relative inline-block group">
                                                                <!-- Icoon -->
                                                                <i class="fa-solid fa-circle-info text-gray-600 hover:text-blue-500 cursor-pointer"></i>

                                                                <!-- Tooltip -->
                                                                <div class="absolute right-0 top-full mt-1 w-56 bg-gray-700 text-white text-sm p-2 rounded shadow-lg
                                                                    opacity-0 pointer-events-none group-hover:opacity-100 group-hover:pointer-events-auto
                                                                    transition-opacity duration-200 z-50">
                                                                    {{ $tooltips[$option] }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>

                                                <!-- Input velden -->



                                                @if(in_array($option, $selectedPanelOption[$index]))

                                                    @if($option == 4)
                                                        <label><strong>{{ __('messages.Ruimte bovenkant tot vrije ruimte') }}</strong></label>
                                                        <input type="number"
                                                               wire:model="panelValues.{{$index}}.4_1"
                                                               wire:keydown="updatePanelValues({{$index}}, '4_1')"
                                                               placeholder="Vul waarde in"
                                                               class="border rounded px-2 py-1 w-full mt-1">
                                                        @error('panelValues.'.$index.'.4_1')
                                                        <div class="text-red-500 text-sm">{{ $message }}</div>
                                                        @enderror
                                                        <label><strong>{{ __('messages.Vrije ruimte') }}</strong></label>
                                                        <input type="number"
                                                               wire:model="panelValues.{{$index}}.4_2"
                                                               wire:keydown="updatePanelValues({{$index}}, '4_2')"
                                                               placeholder="Vul waarde in"
                                                               class="border rounded px-2 py-1 w-full mt-1">
                                                        <div class="text-red-500 text-sm mt-1">
                                                            @error('panelValues.'.$index.'.4_2') {{ $message }} @enderror
                                                        </div>
                                                    @elseif($option == 3)
                                                        <label><strong>{{ $label }} @if($option == 3) in graden @else in mm @endif</strong></label>
                                                        <input type="number"
                                                               wire:model="panelValues.{{$index}}.{{ $option }}"
                                                               wire:keydown="updatePanelValues({{$index}}, {{$option}})"
                                                               placeholder="Vul waarde in"
                                                               class="border rounded px-2 py-1 w-full mt-1">
                                                        @error('panelValues.'.$index.'.'.$option)
                                                        <div class="text-red-500 text-sm">{{ $message }}</div>
                                                        @enderror
                                                    @else
                                                        <label><strong>{{ $label }} @if($option == 3) in graden @else in mm @endif</strong></label>

                                                        <select
                                                            wire:model="panelValues.{{$index}}.{{ $option }}"
                                                            wire:change="updatePanelValues({{$index}}, {{$option}})"
                                                            class="border rounded px-2 py-1 w-full mt-1"
                                                        >
                                                            @if($option != 3)
                                                                @for($i = 20; $i <= 200; $i += 20)
                                                                    <option value="{{ $i }}">{{ $i }} mm</option>
                                                                @endfor
                                                            @else
                                                                @for($i = 20; $i <= 90; $i += 5) <!-- voorbeeld voor graden -->
                                                                <option value="{{ $i }}">{{ $i }}°</option>
                                                                @endfor
                                                            @endif
                                                        </select>

                                                        <div class="text-red-500 text-sm mt-1">
                                                            @error('panelValues.'.$option) {{ $message }} @enderror
                                                        </div>
                                                    @endif


                                                @endif
                                            </label>
                                        @endforeach

                                    </div>


                                    <!-- Rechter div (dynamische afbeelding) -->
                                    <div class="flex-1 flex justify-center mt-[40px]">
                                        <div class="relative md:w-[90%] mx-auto">
                                            <!-- Afbeelding bepaalt de hoogte van de container -->
                                            <img src="{{ asset($panelImages[$index] ?? 'storage/images/rietpanel/paneel.png') }}"
                                                class="w-full h-auto block"
                                                alt="Panel"
                                            />

                                            <!-- Totale maat bovenaan, gecentreerd -->
                                            <div class="absolute top-[-40px] left-[57%] transform -translate-x-1/2 text-[12px] font-bold lg:text-[16px] lg:top-[-50px]">
                                                {{ __('messages.Totale maat') }}: @if($totaleLengte[$index]) < {{$totaleLengte[$index]}}  mm > @else  < 0 mm > @endif
                                            </div>

                                            <!-- LB label links bovenin -->
                                            @if(in_array(1, $selectedPanelOption[$index]))
                                                <div class="absolute top-[-18px] left-[0%] text-[12px] font-bold md:left-[10%] lg:text-[16px] lg:top-[-24px]">
                                                    {{ $this->panelValues[$index]['1'].' mm' ?? '0 mm' }}
                                                </div>
                                            @endif

                                            @if(in_array(4, $selectedPanelOption[$index]))
                                                <div class="absolute top-[-18px] left-[15%] text-[12px] font-bold md:left-[20%] lg:left-[22%] @if(in_array(2, $selectedPanelOption[$index])) xl:left-[25%] @else xl:left-[28%] @endif lg:top-[-24px] lg:text-[16px]">
                                                    < {{ $this->panelValues[$index]['4_1'].' mm >' ?? ' 0 mm >' }}
                                                </div>
                                                <div class="absolute top-[-18px] left-[40%] text-[12px] font-bold md:left-[43%] @if(in_array(2, $selectedPanelOption[$index])) xl:left-[45%] @else xl:left-[50%] @endif lg:top-[-24px] lg:text-[16px]">
                                                    < {{ $this->panelValues[$index]['4_2'].' mm >' ?? '0 mm >' }}
                                                </div>
                                            @endif

                                            <!-- CB label rechts onderin -->
                                            @if(in_array(2, $selectedPanelOption[$index]))
                                                <div class="absolute top-[40px] right-0 mr-2 mb-2 text-[12px] font-bold sm:top-[50px] md:top-[60px] md:right-[-5px] lg:top-[75px] xl:top-[110px] lg:text-[16px] 2xl:top-[135px] 2xl:right-[25px]">
                                                    {{ $this->panelValues[$index]['2'].' mm' ?? '0 mm' }}
                                                </div>
                                            @endif

                                            @if(in_array(3, $selectedPanelOption[$index]))
                                                <div class="absolute top-[40px] left-0 mr-2 mb-2 text-[12px] font-bold sm:top-[50px] md:top-[70px] lg:top-[85px] xl:top-[135px] 2xl:top-[170px] lg:text-[16px]">
                                                    {{ $this->panelValues[$index]['3'] ?? 0 }} &deg;
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                        @endforeach
                        <div class="text-right">

                            <button wire:click="addOfferteLine()" type="button" class="text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-gray-700 dark:border-gray-700">
                                <i class="fa fa-plus hover:cursor-pointer"></i>{{ __('messages.Paneel toevoegen') }}
                            </button>

                        </div>

                        <button wire:loading.attr="disabled" wire:target="saveOfferte" wire:click.prevent="saveOfferte()" @if(!count($this->offerteLines)) disabled @endif class="text-white bg-[#C0A16E] mt-10 hover:bg-[#d1b079] disabled:bg-[#c0a16e99] disabled:cursor-not-allowed hover:cursor-pointer focus:outline-none font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center">
                            <div wire:loading wire:target="saveOfferte">
                                <i class="fa-solid fa-spinner fa-spin"></i>{{ __('messages.Offerte plaatsen') }}
                            </div>
                            <div wire:loading.attr="hidden" wire:target="saveOfferte">
                                {{ __('messages.Offerte opslaan') }}
                            </div>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

