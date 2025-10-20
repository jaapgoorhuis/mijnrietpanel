
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
                    <p class="ms-1 text-sm font-medium text-gray-700 md:ms-2">Mijn bedrijf > {{$this->company->bedrijfsnaam}}</p>
                </div>
            </li>
        </ol>
    </nav>
</x-slot>


<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(Session::has('success'))
            <div id="alert-3" class="flex items-center p-4 mb-4 text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
                <svg class="shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                </svg>
                <span class="sr-only">Info</span>
                <div class="ms-3 text-sm font-medium">
                    {{ session('success') }}
                </div>
                <button type="button" class="ms-auto -mx-1.5 -my-1.5 bg-green-50 text-green-500 rounded-lg focus:ring-2 focus:ring-green-400 p-1.5 hover:bg-green-200 inline-flex items-center justify-center h-8 w-8 dark:bg-gray-800 dark:text-green-400 dark:hover:bg-gray-700" data-dismiss-target="#alert-3" aria-label="Close">
                    <span class="sr-only">Close</span>
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                </button>
            </div>
        @endif

                @if(Session::has('error'))
                    <div id="alert-3" class="flex items-center p-4 mb-4 text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
                        <svg class="shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                        </svg>
                        <span class="sr-only">Info</span>
                        <div class="ms-3 text-sm font-medium">
                            {{ session('error') }}
                        </div>
                        <button type="button" class="ms-auto -mx-1.5 -my-1.5 bg-green-50 text-green-500 rounded-lg focus:ring-2 focus:ring-green-400 p-1.5 hover:bg-green-200 inline-flex items-center justify-center h-8 w-8 dark:bg-gray-800 dark:text-green-400 dark:hover:bg-gray-700" data-dismiss-target="#alert-3" aria-label="Close">
                            <span class="sr-only">Close</span>
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                            </svg>
                        </button>
                    </div>
                @endif
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h2 class="text-lg font-medium text-gray-900">
                    Bedrijfslogo
                </h2>

                    <br/>
                    <small>dit logo komt op gemaakte offertes en orders te staan.</small>

                    <div class="grid md:grid-cols-10">
                        <div class="md:col-span-9">
                            <input wire:model="files" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" id="file_input" type="file">
                            @error('files') <span class="error">{{ $message }}</span> @enderror
                        </div>
                        <div class="md:col-span-1 text-right pt-[10px] md:pt-0">
                            <button wire:loading.attr="disabled" wire:target="files" wire:click="uploadCompanyLogo()" type="button" class="disabled:cursor-not-allowed w-[100%] md:w-auto text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-gray-700 dark:border-gray-700">
                                <div wire:loading wire:target="files">
                                    <i class="fa-solid fa-spinner fa-spin"></i>
                                </div>
                                <i wire:loading.attr="hidden" wire:target="files" class="fa-solid fa-upload"></i>
                            </button>
                        </div>
                    </div>
                    <br/>
                    <br/>
                    <label>Huidige logo</label><br/>
                    <img src="{{asset('storage/companylogos/'.$this->company->logo)}}" class="h-[75px]"/>
                </div>
        </div>

    <br/>
        @reseller
        @if(!\Illuminate\Support\Facades\Auth::user()->is_architect)
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h2 class="text-lg font-medium text-gray-900">
                    Prijsregels
                </h2>
                <button wire:click="priceRules()" type="button" class="w-full sm:w-auto mt-[10px] text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5  dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-gray-700 dark:border-gray-700">
                    <i class="fa-solid fa-money-check-dollar"></i> Ga naar prijsregels
                </button>
            </div>
        </div>
        @endif
        @endreseller

        @userNonReseller
        @if(!\Illuminate\Support\Facades\Auth::user()->is_architect)
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h2 class="text-lg font-medium text-gray-900">
                    Jouw inkoopprijs
                </h2>
                <div class="overflow-x-auto">
                    <table id="pricerules-table" class="w-full text-sm text-left text-gray-500 dark:text-gray-400 mt-[25px]">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-4 py-3">Id:</th>
                            <th scope="col" class="px-4 py-3">Regel:</th>
                            <th scope="col" class="px-4 py-3">Dikte:</th>
                            <th scope="col" class="px-4 py-3">Prijs:</th>
                        </tr>
                        </thead>
                        <tbody>

                            @foreach($this->basicPriceRules as $priceRule)
                                <tr class="border-b ">
                                    <th scope="row" class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap">{{$priceRule->id}}</th>
                                    <td class="px-4 py-3">{{$priceRule->rule_name}}</td>
                                    <td class="px-4 py-3">{{$priceRule->PanelType->name}} </td>
                                    <td class="px-4 py-3">
                                        <?php $discount = $this->companyDiscount /100 * $priceRule->price;?>
                                        € {{$priceRule->price - $discount}},- excl. BTW
                                    </td>

                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
        @enduserNonReseller
        <br/>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h2 class="text-lg font-medium text-gray-900">
                            Bedrijfsgegevens updaten
                        </h2>
                        <div class="grid md:grid-cols-2 md:gap-6">
                            <div class="relative z-0 w-full mb-5 group">
                                <label for="straat" class="text-gray-400">straat *</label>
                                <input type="text" wire:model="straat" name="straat" id="straat" class="block py-2.5 px-0 w-full text-md text-gray-900 border-0 border-b-2 border-gray-300 appearance-none dark:text-gray-900 dark:border-gray-600 focus:outline-none focus:ring-0 focus:border-b-[#C0A16E]" placeholder=" " />
                            </div>
                            <div class="relative z-0 w-full mb-5 group">
                                <label for="postcode" class="text-gray-400">postcode *</label>
                                <input type="text" wire:model="postcode" name="postcode" id="postcode" class="block py-2.5 px-0 w-full text-md text-gray-900 border-0 border-b-2 border-gray-300 appearance-none dark:text-gray-900 dark:border-gray-600 focus:outline-none focus:ring-0 focus:border-b-[#C0A16E]" placeholder=" " />
                            </div>
                        </div>


                        <div class="relative z-0 w-full mb-5 group">
                            <label for="plaats" class="text-gray-400">plaats *</label>
                            <input type="text" wire:model="plaats" name="plaats" id="plaats" class="block py-2.5 px-0 w-full text-md text-gray-900 border-0 border-b-2 border-gray-300 appearance-none dark:text-gray-900 dark:border-gray-600 focus:outline-none focus:ring-0 focus:border-b-[#C0A16E]" placeholder=" " />
                        </div>


                        <button wire:click="updateCompany()" type="button" class="w-full sm:w-auto mt-[10px] text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5  dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-gray-700 dark:border-gray-700">
                            Opslaan
                        </button>
                    </div>
                </div>
    </div>

</div>
