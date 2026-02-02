
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
                    <a href="/supliers" class="inline-flex items-center md:ms-2 text-sm font-medium text-gray-700 hover:text-[#C0A16E] ">
                        Leveranciers
                    </a>
                </div>
            </li>

            <li>
                <div class="flex items-center">
                    <i class="fa-solid fa-angle-right"></i>
                    <p class="ms-1 text-sm font-medium text-gray-700 md:ms-2">Leverancier aanmaken</p>
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
                            <i wire:click="cancelCreateSuplier()" class="absolute right-0 fa-solid fa-xmark text-xl hover:cursor-pointer"></i>
                        </div>

                        Leverancier aanmaken
                        <br/><br/>
                        <div class="grid md:grid-cols-2 md:gap-6">
                            <div class="relative z-0 w-full mb-5 group">
                                <label for="suplier_name" class="text-gray-400">Leverancier naam *</label>
                                <input type="text"  wire:model="suplier_name" name="suplier_name" id="suplier_name" class="block py-2.5 px-0 w-full text-md text-gray-900 border-0 border-b-2 border-gray-300 appearance-none dark:text-gray-900 dark:border-gray-600 focus:outline-none focus:ring-0 focus:border-b-[#C0A16E]" placeholder=" " required />
                                <div class="text-red-500">@error('suplier_name') {{ $message }} @enderror</div>
                            </div>
                            <div class="relative z-0 w-full mb-5 group">
                                <label for="suplier_email" class="text-gray-400">Leverancier email *</label>
                                <input type="text"  wire:model="suplier_email" name="suplier_email" id="suplier_email" class="block py-2.5 px-0 w-full text-md text-gray-900 border-0 border-b-2 border-gray-300 appearance-none dark:text-gray-900 dark:border-gray-600 focus:outline-none focus:ring-0 focus:border-b-[#C0A16E]" placeholder=" " required />
                                <div class="text-red-500">@error('suplier_email') {{ $message }} @enderror</div>
                            </div>
                        </div>


                            <div class="grid md:grid-cols-2 md:gap-6">
                                <div class="relative z-0 w-full mb-5 group">
                                    <label for="suplier_straat" class="text-gray-400">Leverancier straat *</label>
                                    <input type="text" wire:model="suplier_straat" name="suplier_straat" id="suplier_straat" class="block py-2.5 px-0 w-full text-md text-gray-900 border-0 border-b-2 border-gray-300 appearance-none dark:text-gray-900 dark:border-gray-600 focus:outline-none focus:ring-0 focus:border-b-[#C0A16E]" placeholder=" " />
                                    <div class="text-red-500">@error('suplier_straat') {{ $message }} @enderror</div>
                                </div>
                                <div class="relative z-0 w-full mb-5 group">
                                    <label for="suplier_postcode" class="text-gray-400">Leverancier postcode *</label>
                                    <input type="text" wire:model="suplier_postcode" name="suplier_postcode" id="suplier_postcode" class="block py-2.5 px-0 w-full text-md text-gray-900 border-0 border-b-2 border-gray-300 appearance-none dark:text-gray-900 dark:border-gray-600 focus:outline-none focus:ring-0 focus:border-b-[#C0A16E]" placeholder=" " />
                                    <div class="text-red-500">@error('suplier_postcode') {{ $message }} @enderror</div>
                                </div>
                            </div>

                            <div class="grid md:grid-cols-2 md:gap-6">
                                <div class="relative z-0 w-full mb-5 group">
                                    <label for="suplier_plaats" class="text-gray-400">Leverancier plaats *</label>
                                    <input type="text" wire:model="suplier_plaats" name="suplier_plaats" id="suplier_plaats" class="block py-2.5 px-0 w-full text-md text-gray-900 border-0 border-b-2 border-gray-300 appearance-none dark:text-gray-900 dark:border-gray-600 focus:outline-none focus:ring-0 focus:border-b-[#C0A16E]" placeholder=" " />
                                    <div class="text-red-500">@error('suplier_plaats') {{ $message }} @enderror</div>
                                </div>
                                <div class="relative z-0 w-full mb-5 group">
                                    <label for="suplier_land" class="text-gray-400">Leverancier land *</label>
                                    <input type="text" wire:model="suplier_land" name="suplier_land" id="suplier_land" class="block py-2.5 px-0 w-full text-md text-gray-900 border-0 border-b-2 border-gray-300 appearance-none dark:text-gray-900 dark:border-gray-600 focus:outline-none focus:ring-0 focus:border-b-[#C0A16E]" placeholder=" " />
                                    <div class="text-red-500">@error('suplier_land') {{ $message }} @enderror</div>
                                </div>
                            </div>
                        <br/>
                        <br/>
                        <div class="relative z-0 w-full mb-5 group">
                            <label for="name" class="text-gray-400">Paneel naam *</label>
                            <input type="text"  wire:model="name" name="name" id="name" class="block py-2.5 px-0 w-full text-md text-gray-900 border-0 border-b-2 border-gray-300 appearance-none dark:text-gray-900 dark:border-gray-600 focus:outline-none focus:ring-0 focus:border-b-[#C0A16E]" placeholder=" " required />
                            <div class="text-red-500">@error('name') {{ $message }} @enderror</div>
                        </div>

                        <div class="grid md:grid-cols-2 md:gap-6">
                            <div class="relative z-0 w-full mb-5 group">
                                <label for="toepassing_wand" class="text-gray-400">Geschikt voor wand *</label>
                                <select id="toepassing_wand" wire:model="toepassing_wand" class="block py-2.5 px-0 w-full text-sm text-gray-500 bg-transparent border-0 border-b-2 border-gray-200 appearance-none dark:text-gray-900 focus:outline-none focus:ring-0 focus:border-gray-200 peer">
                                    <option value="0">Nee</option>
                                    <option value="1">Ja</option>
                                </select>
                            </div>

                            <div class="relative z-0 w-full mb-5 group">
                                <label for="toepassing_dak" class="text-gray-400">Geschikt voor dak *</label>
                                <select id="toepassing_dak" wire:model="toepassing_dak" class="block py-2.5 px-0 w-full text-sm text-gray-500 bg-transparent border-0 border-b-2 border-gray-200 appearance-none dark:text-gray-900 focus:outline-none focus:ring-0 focus:border-gray-200 peer">
                                    <option value="0">Nee</option>
                                    <option value="1">Ja</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid md:grid-cols-2 md:gap-6">
                            <div class="relative z-0 w-full mb-5 group">
                                <label for="werkende_breedte" class="text-gray-400">Werkende breedte *</label>
                                <input type="number" wire:model="werkende_breedte" name="werkende_breedte" id="werkende_breedte" class="block py-2.5 px-0 w-full text-md text-gray-900 border-0 border-b-2 border-gray-300 appearance-none dark:text-gray-900 dark:border-gray-600 focus:outline-none focus:ring-0 focus:border-b-[#C0A16E]" placeholder=" " />
                                <div class="text-red-500">@error('werkende_breedte') {{ $message }} @enderror</div>
                            </div>
                            <div class="relative z-0 w-full mb-5 group">
                                <label for="status" class="text-gray-400">Status *</label>
                                <select id="status" wire:model="status" class="block py-2.5 px-0 w-full text-sm text-gray-500 bg-transparent border-0 border-b-2 border-gray-200 appearance-none dark:text-gray-900 focus:outline-none focus:ring-0 focus:border-gray-200 peer">
                                    <option value="0">Non actief</option>
                                    <option value="1">Actief</option>
                                </select>
                            </div>
                        </div>

                            <button wire:loading.attr="disabled" wire:click.prevent="createSuplier()" class="text-white bg-[#C0A16E] mt-10 hover:bg-[#d1b079] disabled:bg-[#c0a16e99] disabled:cursor-not-allowed hover:cursor-pointer focus:outline-none font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center">Leverancier aanmaken</button>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

