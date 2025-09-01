
<x-slot name="header">
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
            <li class="inline-flex items-center">
                <a href="/dashboard" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-[#C0A16E]">
                    Mijn rietpanel
                </a>
            </li>

            <li class="inline-flex items-center">
                <a href="/users" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-[#C0A16E]">
                    Gebruikers
                </a>
            </li>

            <li>
                <div class="flex items-center">
                    <i class="fa-solid fa-angle-right"></i>
                    <p class="ms-1 text-sm font-medium text-gray-700 md:ms-2">Gebruiker bewerken</p>
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
                           <i wire:click="cancelEditUser()" class="absolute right-0 fa-solid fa-xmark text-xl hover:cursor-pointer"></i>
                        </div>

                        Gebruiker bewerken
                        <br/><br/>


                        <div class="grid md:grid-cols-2 md:gap-6">
                            <div class="relative z-0 w-full mb-5 group">
                                <label for="gebruikersnaam" class="text-gray-400">Gebruikersnaam</label>
                                <input type="text" wire:model="gebruikersnaam" name="gebruikersnaam" id="gebruikersnaam" class="block py-2.5 px-0 w-full text-md text-gray-900 border-0 border-b-2 border-gray-300 appearance-none dark:text-gray-900 dark:border-gray-600 focus:outline-none focus:ring-0 focus:border-b-[#C0A16E]" placeholder=" " required />
                                <div class="text-red-500">@error('gebruikersnaam') {{ $message }} @enderror</div>
                            </div>

                            <div class="relative z-0 w-full mb-5 group">
                                <label for="intaker_name" class="text-gray-400">E-mail</label>
                                <input type="text" wire:model="email" name="email" id="email" class="block py-2.5 px-0 w-full text-md text-gray-900 border-0 border-b-2 border-gray-300 appearance-none dark:text-gray-900 dark:border-gray-600 focus:outline-none focus:ring-0 focus:border-b-[#C0A16E]" placeholder=" " required />
                                <div class="text-red-500">@error('email') {{ $message }} @enderror</div>
                            </div>
                        </div>

                        <div class="grid md:grid-cols-2 md:gap-6">
                            <div class="relative z-0 w-full mb-5 group">
                                <label for="phone" class="text-gray-400">Telefoonnummer</label>
                                <input type="text" wire:model="phone" name="phone" id="phone" class="block py-2.5 px-0 w-full text-md text-gray-900 border-0 border-b-2 border-gray-300 appearance-none dark:text-gray-900 dark:border-gray-600 focus:outline-none focus:ring-0 focus:border-b-[#C0A16E]" placeholder=" " required />
                                <div class="text-red-500">@error('phone') {{ $message }} @enderror</div>
                            </div>
                            <div class="relative z-0 w-full mb-5 group">
                                <label for="gebruikersnaam" class="text-gray-400">Bedrijfsnaam</label>
                                <input type="text" wire:model="bedrijfsnaam" name="bedrijfsnaam" id="bedrijfsnaam" class="block py-2.5 px-0 w-full text-md text-gray-900 border-0 border-b-2 border-gray-300 appearance-none dark:text-gray-900 dark:border-gray-600 focus:outline-none focus:ring-0 focus:border-b-[#C0A16E]" placeholder=" " required />
                                <div class="text-red-500">@error('bedrijfsnaam') {{ $message }} @enderror</div>
                            </div>
                        </div>

                        <div class="grid md:grid-cols-2 md:gap-6">
                            <div class="relative z-0 w-full mb-5 group">
                                <label for="status" class="text-gray-400">Status</label>
                                <select id="status" wire:model="status" class="block py-2.5 px-0 w-full text-sm text-gray-500 bg-transparent border-0 border-b-2 border-gray-200 appearance-none dark:text-gray-900 dark:border-gray-700 focus:outline-none focus:ring-0 focus:border-gray-200 peer">
                                    <option value="0" @if($this->status == 0) selected @endif>Non actief</option>
                                    <option value="1" @if($this->status) selected @endif >Actief</option>
                                </select>
                            </div>
                            @admin
                            <div class="relative z-0 w-full mb-5 group">
                                <label for="is_admin" class="text-gray-400">admin</label>
                                <select id="is_admin" wire:model="is_admin" class="block py-2.5 px-0 w-full text-sm text-gray-500 bg-transparent border-0 border-b-2 border-gray-200 appearance-none dark:text-gray-900 dark:border-gray-700 focus:outline-none focus:ring-0 focus:border-gray-200 peer">
                                    <option value="0" @if($this->status == 0) selected @endif>Nee</option>
                                    <option value="1" @if($this->status) selected @endif >Ja</option>
                                </select>
                            </div>
                            @endadmin
                        </div>

                        <button wire:loading.attr="disabled" wire:click.prevent="updateUser({{$this->user->id}})" class="text-white bg-[#C0A16E] mt-10 hover:bg-[#d1b079] disabled:bg-[#c0a16e99] disabled:cursor-not-allowed hover:cursor-pointer focus:outline-none font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center">Gebruiker updaten</button>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

