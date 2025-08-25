
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
                    <a href="/orders" class="inline-flex items-center md:ms-2 text-sm font-medium text-gray-700 hover:text-[#C0A16E] ">
                        Mijn orders
                    </a>
                </div>
            </li>

            <li>
                <div class="flex items-center">
                    <i class="fa-solid fa-angle-right"></i>
                    <p class="ms-1 text-sm font-medium text-gray-700 md:ms-2">Nieuwe order aanmaken</p>
                </div>
            </li>
        </ol>
    </nav>
</x-slot>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="grid">
                    <form>
                        <div class="relative">
                            <i wire:click="cancelCreateOrder()" class="absolute right-0 fa-solid fa-xmark text-xl hover:cursor-pointer"></i>
                        </div>

                        Project gegevens
                        <br/><br/>
                        <div class="relative z-0 w-full mb-5 group">
                            <label for="project_name" class="text-gray-400">Project naam *</label>
                            <input type="text"  wire:model="project_naam" name="project_naam" id="project_naam" class="block py-2.5 px-0 w-full text-md text-gray-900 border-0 border-b-2 border-gray-300 appearance-none dark:text-gray-900 dark:border-gray-600 focus:outline-none focus:ring-0 focus:border-b-[#C0A16E]" placeholder=" " required />
                            <div class="text-red-500">@error('project_naam') {{ $message }} @enderror</div>
                        </div>

                        <div class="grid md:grid-cols-2 md:gap-6">
                            <div class="relative z-0 w-full mb-5 group">
                                <label for="project_adres" class="text-gray-400">Project adres</label>
                                <input type="text" wire:model="project_adres" name="project_adres" id="project_adres" class="block py-2.5 px-0 w-full text-md text-gray-900 border-0 border-b-2 border-gray-300 appearance-none dark:text-gray-900 dark:border-gray-600 focus:outline-none focus:ring-0 focus:border-b-[#C0A16E]" placeholder=" " />
                            </div>
                            <div class="relative z-0 w-full mb-5 group">
                                <label for="intaker_name" class="text-gray-400">Uw naam *</label>
                                <input type="text" wire:model="intaker" name="intaker" id="intaker" class="block py-2.5 px-0 w-full text-md text-gray-900 border-0 border-b-2 border-gray-300 appearance-none dark:text-gray-900 dark:border-gray-600 focus:outline-none focus:ring-0 focus:border-b-[#C0A16E]" placeholder=" " required />
                                <div class="text-red-500">@error('intaker') {{ $message }} @enderror</div>
                            </div>
                        </div>

                        <br/>

                        <br/>

                        @foreach($orderLines as $index => $orders)
                            @if($index > 0)
                            <hr class="border-2 border-[#C0A16E]"/><br/><br/>
                            @endif
                            <div class="text-right">
                                <button wire:click.prevent="removeOrderLine({{$index}})" type="button" class="text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2">
                                    <i class="fa-solid fa-trash hover:cursor-pointer text-white"></i>
                                </button>


                            </div>
                            Paneel optie's<br/><br/>
                            <div class="relative z-0 w-full mb-5 group">
                                <label for="rietkleur" class="text-gray-400">Rietkleur *</label>
                                <select id="rietkleur" wire:model="rietkleur.{{$index}}" class="block py-2.5 px-0 w-full text-sm text-gray-500 bg-transparent border-0 border-b-2 border-gray-200 appearance-none dark:text-gray-900 focus:outline-none focus:ring-0 focus:border-gray-200 peer">
                                    <option value="oldlook">Oude look</option>
                                    <option value="newlook">Nieuwe look</option>
                                </select>
                            </div>
                            <div class="grid md:grid-cols-2 md:gap-6">
                                <div class="relative z-0 w-full mb-5 group">
                                    <label for="toepassing" class="text-gray-400">Toepassing *</label>
                                    <select id="toepassing" wire:model="toepassing.{{$index}}" wire:change="updateBrands({{$index}})" class="block py-2.5 px-0 w-full text-sm text-gray-500 bg-transparent border-0 border-b-2 border-gray-200 appearance-none dark:text-gray-900 dark:border-gray-700 focus:outline-none focus:ring-0 focus:border-gray-200 peer">
                                        <option value="wand">Wand</option>
                                        <option value="dak">Dak</option>
                                    </select>
                                </div>


                                <div class="relative z-0 w-full mb-5 group">
                                    <label for="merk_paneel" class="text-gray-400">Merk paneel *</label>
                                    <select id="merk_paneel" wire:change="updateM2({{$index}})" wire:model="merk_paneel.{{$index}}" class="block py-2.5 px-0 w-full text-sm text-gray-500 bg-transparent border-0 border-b-2 border-gray-200 appearance-none dark:text-gray-900 dark:border-gray-700 focus:outline-none focus:ring-0 focus:border-gray-200 peer">
                                      @foreach($this->brands[$index] as $key => $brands)
                                        <option value="{{$key}}">{{$key}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <br/>
                            Afmetingen paneel<br/><br/>
                            <div class="grid md:grid-cols-2 md:gap-6">
                                <div class="relative z-0 w-full mb-5 group">
                                    <label for="fillLb" class="text-gray-400">LB (max 210mm)
                                        <div class="tooltip">
                                            <div class="tooltip-content">
                                                Vul hier de LB in mm in. De maximale LB mag 210mm zijn. Laat dit op nul staan als de LB niet van toepassing is. Heeft u toch een grotere LB nodig? Neem dan contact met ons op.
                                            </div>
                                            <i wire:click.prevent="" class="fa-solid fa-circle-info hover:cursor-pointer" id="tooltip{{$index}}"></i>
                                        </div>
                                    </label>
                                    <input type="number" min="0" max="210" wire:model="fillLb.{{$index}}" wire:keydown="updateLb({{$index}})" name="fillLb" id="fillLb" class="focus:border-b-[#C0A16E] block py-2.5 px-0 w-full text-md text-gray-900 border-0 border-b-2 border-gray-300 appearance-none dark:text-gray-900 dark:border-gray-600 focus:outline-none focus:ring-0" placeholder=" " required />
                                </div>
                                <div class="relative z-0 w-full mb-5 group">
                                    <label for="fillLb" class="text-gray-400">CB (max 200mm)
                                        <div class="tooltip">
                                            <div class="tooltip-content">
                                               Vul hier de CB in mm in. De maximale CB mag 200mm zijn. Laat dit op nul staan als de CB niet van toepassing is. Heeft u toch een grotere CB nodig? Neem dan contact met ons op.
                                            </div>
                                            <i wire:click.prevent="" class="fa-solid fa-circle-info hover:cursor-pointer"></i>
                                        </div>
                                    </label>
                                    <input type="number" min="0" max="200" wire:model="fillCb.{{$index}}" wire:keydown="updateCb({{$index}})" name="fillCb" id="fillCb" class="focus:border-b-[#C0A16E] block py-2.5 px-0 w-full text-md text-gray-900 border-0 border-b-2 border-gray-300 appearance-none dark:text-gray-900 dark:border-gray-600 focus:outline-none focus:ring-0" placeholder=" " required />
                                </div>
                            </div>

                            <div class="grid md:grid-cols-2 md:gap-6">
                                <div class="relative z-0 w-full mb-5 group">
                                    <label for="fillTotaleLengte" class="text-gray-400">Totale paneellengte (mm) *
                                        <div class="tooltip" wire:ignore>
                                            <div class="tooltip-content">
                                                Vul hier de totale paneel lengte in mm in, inclusief de LB & de CB in.
                                            </div>
                                            <i wire:click.prevent="" class="fa-solid fa-circle-info hover:cursor-pointer"></i>
                                        </div>
                                    </label>
                                    <input type="number" min="1" wire:model="fillTotaleLengte.{{$index}}" wire:change="updateTotaleLengte({{$index}})" wire:keydown="updateTotaleLengte({{$index}})" name="fillTotaleLengte" id="fillTotaleLengte" class="focus:border-b-[#C0A16E] block py-2.5 px-0 w-full text-md text-gray-900 border-0 border-b-2 border-gray-300 appearance-none dark:text-gray-900 dark:border-gray-600 focus:outline-none focus:ring-0" placeholder=" " required />
                                </div>
                                <div class="relative z-0 w-full mb-5 group">
                                    <label for="fillTotaleLengte" class="text-gray-400">Aantal panelen *
                                        <div class="tooltip">
                                            <div class="tooltip-content">
                                                Vul hier het aantal panelen in welke u nodig heeft met de ingevulde specificaties. Heeft u meerdere panelen nodig met andere specificaties? Druk dan op de plus hieronder om een extra rij aan te maken.
                                            </div>
                                            <i wire:click.prevent="" class="fa-solid fa-circle-info hover:cursor-pointer"></i>
                                        </div>
                                    </label>
                                    <input type="number" min="1" wire:change="updateM2({{$index}})" wire:keydown="updateM2({{$index}})" wire:model="aantal.{{$index}}"  name="aantal" id="aantal" class="focus:border-b-[#C0A16E] block py-2.5 px-0 w-full text-md text-gray-900 border-0 border-b-2 border-gray-300 appearance-none dark:text-gray-900 dark:border-gray-600 focus:outline-none focus:ring-0" placeholder=" " required /></div>
                            </div>
                            <div class="relative z-0 w-full mb-5 group">
                                <label for="kerndikte" class="text-gray-400">Kerndikte *</label>
                                <select id="kerndikte" wire:model="kerndikte.{{$index}}" class="block py-2.5 px-0 w-full text-sm text-gray-500 bg-transparent border-0 border-b-2 border-gray-200 appearance-none dark:text-gray-400 dark:border-gray-700 focus:outline-none focus:ring-0 focus:border-gray-200 peer">
                                    <option value="60mm">60mm</option>
                                    <option value="80mm">80mm</option>
                                    <option value="100mm">100mm</option>
                                    <option value="120mm">120mm</option>
                                    <option value="140mm">140mm</option>
                                    <option value="160mm">160mm</option>
                                </select>
                            </div>

                            <div class="text-right">
                                Vierkante meters: <strong>{{$this->m2[$index]}} m²</strong>
                            </div>
                            <br/><br/><br/>
                            <div style="background-image: url('/public/storage/images/rietpanel_panel.png');" class="mb-[30px] relative bg-contain bg-no-repeat bg-center w-full h-[75px] sm:h-[100px] md:h-[120px] lg:h-[140px] xl:h-[160px]">

                                <div class="absolute right-0 bottom-[-10px] sm:bottom-[10px] md:bottom-[20px] sm:right-[10px] md:right-[10px] lg:right-[45px] xl:right-[50px] text-[11px] md:text-[15px]">
                                   <strong>CB:</strong> {{$this->cb[$index]}}<span class="md:hidden lg:hidden xl:hidden sm:hidden"><br/></span>mm
                                </div>
                                <div class="absolute left-[60px] sm:left-[120px] md:left-[140px] lg:left-[230px] xl:left-[270px] text-[11px] md:text-[15px] top-[-10px]"><strong>LB:</strong> {{$this->lb[$index]}}mm</div>
                                <div class="absolute top-[-10px] left-[40%] md:left-[45%] text-[11px] md:text-[15px]"><strong><- Totale maat: </strong>{{$this->totaleLengte[$index]}} mm -> </div>
                            </div>
                        @endforeach
                        <div class="text-right">

                            <button wire:click="addOrderLine()" type="button" class="text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-gray-700 dark:border-gray-700">
                                <i class="fa fa-plus hover:cursor-pointer"></i> Paneel toevoegen
                            </button>

                        </div>

                        <button wire:loading.attr="disabled" wire:target="saveOrder" wire:click.prevent="saveOrder()" @if(!count($this->orderLines)) disabled @endif class="text-white bg-[#C0A16E] mt-10 hover:bg-[#d1b079] disabled:bg-[#c0a16e99] disabled:cursor-not-allowed hover:cursor-pointer focus:outline-none font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center">
                            <div wire:loading wire:target="saveOrder">
                               <i class="fa-solid fa-spinner fa-spin"></i> Order plaatsen
                            </div>
                            <div wire:loading.attr="hidden" wire:target="saveOrder">
                                Order plaatsen
                            </div>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

