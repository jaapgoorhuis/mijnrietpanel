
<x-slot name="header">
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
            <li class="inline-flex items-center">
                <a href="/dashboard" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-[#C0A16E]">
                    Mijn rietpanel
                </a>
            </li>
            @admin
            <li>
                <div class="flex items-center">
                    <i class="fa-solid fa-angle-right"></i>
                    <a href="/companys" class="inline-flex items-center md:ms-2 text-sm font-medium text-gray-700 hover:text-[#C0A16E] ">
                        Bedrijven
                    </a>
                </div>
            </li>
            @endadmin
            <li>
                <div class="flex items-center">
                    <i class="fa-solid fa-angle-right"></i>
                    <a href="/companys/pricerules" class="inline-flex items-center md:ms-2 text-sm font-medium text-gray-700 hover:text-[#C0A16E] ">
                        Prijsregels
                    </a>
                </div>
            </li>

            <li>
                <div class="flex items-center">
                    <i class="fa-solid fa-angle-right"></i>
                    <p class="ms-1 text-sm font-medium text-gray-700 md:ms-2">Prijsregel bewerken</p>
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
                            <i wire:click="cancelEditPriceRule()" class="absolute right-0 fa-solid fa-xmark text-xl hover:cursor-pointer"></i>
                        </div>

                        Algemene prijsregels bewerken
                        <br/><br/>



                        <div class="grid md:grid-cols-2 md:gap-6">
                            <div class="relative z-0 w-full mb-5 group">
                                <label for="project_name" class="text-gray-400">Regelnaam *</label>
                                <input type="text"  wire:model="rule_name" name="rule_name" id="rule_name" @if(Auth::user()->is_admin !=1) disabled @endif class="disabled:hover:cursor-not-allowed block py-2.5 px-0 w-full text-md text-gray-900 border-0 border-b-2 border-gray-300 appearance-none dark:text-gray-900 dark:border-gray-600 focus:outline-none focus:ring-0 focus:border-b-[#C0A16E]" placeholder=" " required />
                                <div class="text-red-500">@error('rule_name') {{ $message }} @enderror</div>
                            </div>

                            <div class="relative z-0 w-full mb-5 group">
                                <label for="panel_type" class="text-gray-400">Dikte paneel *</label>
                                <select id="panel_type" wire:model="panel_type" @if(Auth::user()->is_admin != 1) disabled @endif class="disabled:hover:cursor-not-allowed block py-2.5 px-0 w-full text-sm text-gray-500 bg-transparent border-0 border-b-2 border-gray-200 appearance-none dark:text-gray-900 dark:border-gray-700 focus:outline-none focus:ring-0 focus:border-gray-200 peer">
                                    @foreach($this->panel_types as $panel_types)
                                        <option value="{{$panel_types->id}}">{{$panel_types->name}}</option>
                                    @endforeach
                                </select>
                                <div class="text-red-500">@error('panel_type') {{ $message }} @enderror</div>
                            </div>
                        </div>

                        <div class="grid md:grid-cols-2 md:gap-6">
                            @if($this->priceRule->company_id !== 0)
                            <div class="relative z-0 w-full mb-5 group">
                                <label for="rietpanel_panel_price" class="text-gray-400">Rietpanel's prijs per m² *</label>
                                <input type="number" wire:model="rietpanel_panel_price" name="rietpanel_panel_price" id="rietpanel_panel_price" @if(Auth::user()->is_admin != 1) disabled @endif class="disabled:hover:cursor-not-allowed block py-2.5 px-0 w-full text-md text-gray-900 border-0 border-b-2 border-gray-300 appearance-none dark:text-gray-900 dark:border-gray-600 focus:outline-none focus:ring-0 focus:border-b-[#C0A16E]" placeholder=" " required />
                            </div>
                            @endif

                            <div class="relative z-0 w-full mb-5 group">
                                <label for="panel_price" class="text-gray-400">@reseller Jouw @endreseller prijs per m² *</label>
                                <input type="number" wire:model="panel_price" name="panel_price" id="panel_price" class="block py-2.5 px-0 w-full text-md text-gray-900 border-0 border-b-2 border-gray-300 appearance-none dark:text-gray-900 dark:border-gray-600 focus:outline-none focus:ring-0 focus:border-b-[#C0A16E]" placeholder=" " required />
                                <div class="text-red-500">@error('panel_price') {{ $message }} @enderror</div>
                            </div>
                        </div>
                        <button wire:loading.attr="disabled" wire:click.prevent="updatePriceRule()" class="text-white bg-[#C0A16E] mt-10 hover:bg-[#d1b079] disabled:bg-[#c0a16e99] disabled:cursor-not-allowed hover:cursor-pointer focus:outline-none font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center">Regel bewerken</button>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

