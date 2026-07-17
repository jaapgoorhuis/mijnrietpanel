
<x-slot name="header">
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
            <li class="inline-flex items-center">
                <a href="/dashboard" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-[#C0A16E]">
                    Mijn rietpanel
                </a>
            </li>

            <li class="inline-flex items-center">
                <a href="/companys" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-[#C0A16E]">
                    Bedrijven
                </a>
            </li>

            <li>
                <div class="flex items-center">
                    <i class="fa-solid fa-angle-right"></i>
                    <p class="ms-1 text-sm font-medium text-gray-700 md:ms-2">Bedrijf aanmaken</p>
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
                           <i wire:click="cancelAddCompany()" class="absolute right-0 fa-solid fa-xmark text-xl hover:cursor-pointer"></i>
                        </div>

                        Bedrijf aanmaken
                        <br/><br/>


                        <div class="relative z-0 w-full mb-5 group">
                            <label for="project_name" class="text-gray-400">Bedrijfsnaam *</label>
                            <input type="text" wire:model.live="bedrijfsnaam" name="bedrijfsnaam" id="bedrijfsnaam" class="block py-2.5 px-0 w-full text-md text-gray-900 border-0 border-b-2 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-b-[#C0A16E]" placeholder=" " required />
                            <div class="text-red-500">@error('bedrijfsnaam') {{ $message }} @enderror</div>
                        </div>
                        @if ($message)
                            <p class="text-red-500 mt-1">{{ $message }}</p>
                        @endif

                        <div class="relative z-0 w-full mb-5 group">
                            <label for="discount" class="text-gray-400">Korting *</label>
                            <input type="number"  wire:model="discount" name="discount" id="discount" class="block py-2.5 px-0 w-full text-md text-gray-900 border-0 border-b-2 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-b-[#C0A16E]" placeholder=" " required />
                            <div class="text-red-500">@error('discount') {{ $message }} @enderror</div>
                        </div>

                        <div class="relative z-0 w-full mb-5 group">
                            <label for="reseller" class="text-gray-400">Architect bureau</label>
                            <select id="reseller" wire:model="architect_bureau" class="block py-2.5 px-0 w-full text-sm text-gray-500 bg-transparent border-0 border-b-2 border-gray-200 appearance-none focus:outline-none focus:ring-0 focus:border-gray-200 peer">
                                    <option value="0">Nee</option>
                                    <option value="1">Ja</option>
                            </select>
                            <div class="text-red-500">@error('architect_bureau') {{ $message }} @enderror</div>
                        </div>

                        <div class="grid md:grid-cols-2 md:gap-6">
                            <div class="relative z-0 w-full mb-5 group">
                                <label for="straat" class="text-gray-400">straat *</label>
                                <input type="text" wire:model.live="straat" name="straat" id="straat" class="block py-2.5 px-0 w-full text-md text-gray-900 border-0 border-b-2 border-gray-300 appearance-none dark:text-gray-900 dark:border-gray-600 focus:outline-none focus:ring-0 focus:border-b-[#C0A16E]" placeholder=" " />
                                <div class="text-red-500">@error('straat') {{ $message }} @enderror</div>
                                @if ($messageStraat)
                                    <p class="text-red-500 mt-1">{{ $messageStraat }}</p>
                                @endif
                            </div>

                            <div class="relative z-0 w-full mb-5 group">
                                <label for="postcode" class="text-gray-400">postcode *</label>
                                <input type="text" wire:model="postcode" name="postcode" id="postcode" class="block py-2.5 px-0 w-full text-md text-gray-900 border-0 border-b-2 border-gray-300 appearance-none dark:text-gray-900 dark:border-gray-600 focus:outline-none focus:ring-0 focus:border-b-[#C0A16E]" placeholder=" " />
                                <div class="text-red-500">@error('postcode') {{ $message }} @enderror</div>
                            </div>
                        </div>

                        @if($showRestoreCompanyModal)
                            <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-[9999]">
                                <div class="border-2 border-[#C0A16E] bg-white rounded-lg shadow-lg p-6 max-w-md w-full text-center">

                                    <h3 class="text-lg font-bold mb-4">
                                        Bedrijf herstellen
                                    </h3>

                                    <p class="mb-6">
                                        Het bedrijf <strong>{{ $restoreCompany->bedrijfsnaam }}</strong> bestaat al,
                                        maar staat op non-actief.<br><br>

                                        Wil je dit bedrijf herstellen?
                                    </p>

                                    <div class="flex justify-center gap-4">

                                        <button
                                            type="button"
                                            wire:click="$set('showRestoreCompanyModal', false)"
                                            class="px-4 py-2 border rounded"
                                        >
                                            Nee
                                        </button>

                                        <button
                                            type="button"
                                            wire:click="restoreInactiveCompany"
                                            class="px-4 py-2 bg-[#C0A16E] text-white rounded hover:bg-[#d1b079]"
                                        >
                                            Ja, herstellen
                                        </button>

                                    </div>

                                </div>
                            </div>
                        @endif

                        <div class="relative z-0 w-full mb-5 group">
                            <label for="plaats" class="text-gray-400">plaats *</label>
                            <input type="text" wire:model="plaats" name="plaats" id="plaats" class="block py-2.5 px-0 w-full text-md text-gray-900 border-0 border-b-2 border-gray-300 appearance-none dark:text-gray-900 dark:border-gray-600 focus:outline-none focus:ring-0 focus:border-b-[#C0A16E]" placeholder=" " />
                            <div class="text-red-500">@error('plaats') {{ $message }} @enderror</div>
                        </div>

                        <div class="relative z-0 w-full mb-5 group">
                            <label for="plaats" class="text-gray-400">Facturatie email adres </label>
                            <input type="text" wire:model="bill_email" name="bill_email" id="bill_email" class="block py-2.5 px-0 w-full text-md text-gray-900 border-0 border-b-2 border-gray-300 appearance-none dark:text-gray-900 dark:border-gray-600 focus:outline-none focus:ring-0 focus:border-b-[#C0A16E]" placeholder=" " />
                            <div class="text-red-500">@error('bill_email') {{ $message }} @enderror</div>
                        </div>

                        <div class="relative z-0 w-full mb-5 group">
                            <label for="land" class="text-gray-400">Bedrijfstaal</label>
                            <select id="land" wire:model="lang" class="block py-2.5 px-0 w-full text-sm text-gray-500 bg-transparent border-0 border-b-2 border-gray-200 appearance-none dark:text-gray-900 dark:border-gray-700 focus:outline-none focus:ring-0 focus:border-gray-200 peer">

                                <option value="nl">Nederlands</option>
                                <option value="en">Engels</option>

                            </select>

                            <div class="text-red-500">@error('lang') {{ $message }} @enderror</div>
                        </div>


                        <button wire:loading.attr="disabled" wire:click.prevent="createCompany()" class="text-white bg-[#C0A16E] mt-10 hover:bg-[#d1b079] disabled:bg-[#c0a16e99] disabled:cursor-not-allowed hover:cursor-pointer focus:outline-none font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center">Bedrijf toevoegen</button>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
