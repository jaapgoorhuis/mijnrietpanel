
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
    <div class="max-w-8xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="grid">
                    <form>
                        <div class="relative">
                            <i wire:click="cancelChangeOrder()" class="absolute right-0 fa-solid fa-xmark text-xl hover:cursor-pointer"></i>
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
                                            {{ __('messages.Vul hier een percentage marge in. Marge is het percentage winst wat je bovenop de inkoopprijs kunt plaatsen.') }}

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
                                    <div class="tooltip-content">
                                        {{ __('messages.Geef hier aan wanneer er een speciale bewerking of actie vereist is.') }}
                                    </div>
                                    <i wire:click.prevent="" class="fa-solid fa-circle-info hover:cursor-pointer"></i>
                                </div>
                            </label>
                            <textarea wire:model="comment" name="comment" id="comment" class="block py-2.5 px-0 w-full text-md text-gray-900 border-0 border-b-2 border-gray-300 appearance-none dark:text-gray-900 dark:border-gray-600 focus:outline-none focus:ring-0 focus:border-b-[#C0A16E]" placeholder=" "></textarea>
                            <div class="text-red-500">@error('comment') {{ $message }} @enderror</div>
                        </div>

                        <br/><br/>
                        @foreach($orderLines as $index => $orders)
                            @if($index > 0)
                                <hr class="border-2 border-[#C0A16E]"/><br/><br/>
                            @endif
                            <div class="text-right">
                                <button wire:click.prevent="removeOrderLine({{$index}})" type="button" class="text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2">
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

                            <div class="relative z-0 w-full mb-5 group">
                                <label for="fillLb" class="text-gray-400">CB (max 200mm)
                                    <div class="tooltip">
                                        <div class="tooltip-content">
                                            {{ __('messages.Vul hier de CB in mm in. De maximale CB mag 200mm zijn. Laat dit op nul staan als de CB niet van toepassing is. Heeft u toch een grotere CB nodig? Neem dan contact met ons op.') }}
                                        </div>
                                        <i wire:click.prevent="" class="fa-solid fa-circle-info hover:cursor-pointer"></i>
                                    </div>
                                </label>
                                <select id="fillCb" wire:model="fillCb.{{$index}}" wire:change="updateCb({{$index}})" class="block py-2.5 px-0 w-full text-sm text-gray-500 bg-transparent border-0 border-b-2 border-gray-200 appearance-none dark:text-gray-900 focus:outline-none focus:ring-0 focus:border-gray-200 peer">

                                    <option value="0">0mm</option>
                                    <option value="20">20mm</option>
                                    <option value="40">40mm</option>
                                    <option value="60">60mm</option>
                                    <option value="80">80mm</option>
                                    <option value="100">100mm</option>
                                    <option value="120">120mm</option>
                                    <option value="140">140mm</option>
                                    <option value="160">160mm</option>
                                    <option value="180">180mm</option>
                                    <option value="200">200mm</option>

                                </select>

                                <div class="text-red-500">@error('cb.'.$index) {{ $message }} @enderror</div>
                            </div>

                            <div class="grid md:grid-cols-2 md:gap-6">
                                <div class="relative z-0 w-full mb-5 group">
                                    <label for="fillTotaleLengte" class="text-gray-400"> {{ __('messages.Totale paneellengte') }} (mm) *
                                        <div class="tooltip" wire:ignore>
                                            <div class="tooltip-content">
                                                {{ __('messages.Vul hier de totale paneel lengte in mm in, inclusief de CB in. De minimale lengte moet 500mm zijn en de maximale lengte mag 14500mm zijn. Wil je langere lengtes bestellen? Neem dan contact met ons op.') }}
                                            </div>
                                            <i wire:click.prevent="" class="fa-solid fa-circle-info hover:cursor-pointer"></i>
                                        </div>
                                    </label>
                                    <input type="number" value="" min="500" max="14500" wire:model="fillTotaleLengte.{{$index}}" wire:change="updateTotaleLengte({{$index}})" wire:keydown="updateTotaleLengte({{$index}})" name="fillTotaleLengte" id="fillTotaleLengte" class="focus:border-b-[#C0A16E] block py-2.5 px-0 w-full text-md text-gray-900 border-0 border-b-2 border-gray-300 appearance-none dark:text-gray-900 dark:border-gray-600 focus:outline-none focus:ring-0" required />
                                    <div class="text-red-500">@error('totaleLengte.'.$index) {{ $message }} @enderror</div>
                                </div>
                                <div class="relative z-0 w-full mb-5 group">
                                    <label for="fillTotaleLengte" class="text-gray-400">{{ __('messages.Aantal panelen') }} *
                                        <div class="tooltip">
                                            <div class="tooltip-content">
                                                {{ __('messages.Vul hier het aantal panelen in welke u nodig heeft met de ingevulde specificaties. Heeft u meerdere panelen nodig met andere specificaties? Druk dan op de plus hieronder om een extra rij aan te maken.') }}
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
                            <div style="background-image: url('/public/storage/images/rietpanel_panel.png');" class="mb-[30px] relative bg-contain bg-no-repeat bg-center w-full h-[75px] sm:h-[100px] md:h-[120px] lg:h-[140px] xl:h-[160px]">

                                <div class="absolute right-0 bottom-[-10px] sm:bottom-[10px] md:bottom-[20px] sm:right-[10px] md:right-[10px] lg:right-[45px] xl:right-[50px] text-[11px] md:text-[15px]">
                                    <strong>CB:</strong> {{$this->cb[$index]}}<span class="md:hidden lg:hidden xl:hidden sm:hidden"><br/></span>mm
                                </div>
                                {{--                                <div class="absolute left-[60px] sm:left-[120px] md:left-[140px] lg:left-[230px] xl:left-[270px] text-[11px] md:text-[15px] top-[-10px]"><strong>LB:</strong> {{$this->lb[$index]}}mm</div>--}}
                                <div class="absolute top-[-10px] left-[40%] md:left-[45%] text-[11px] md:text-[15px]"><strong><- {{ __('messages.Totale maat') }}: </strong>{{$this->totaleLengte[$index]}} mm -> </div>
                            </div>
                        @endforeach
                        <div class="text-right">

                            <button wire:click="addOrderLine()" type="button" class="text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-gray-700 dark:border-gray-700">
                                <i class="fa fa-plus hover:cursor-pointer"></i>{{ __('messages.Paneel toevoegen') }}
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

