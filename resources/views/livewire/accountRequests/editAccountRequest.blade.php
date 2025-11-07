
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
                    <a href="/accountrequests" class="inline-flex items-center md:ms-2 text-sm font-medium text-gray-700 hover:text-[#C0A16E] ">
                        Account verzoeken
                    </a>
                </div>
            </li>


            <li>
                <div class="flex items-center">
                    <i class="fa-solid fa-angle-right"></i>
                    <p class="ms-1 text-sm font-medium text-gray-700 md:ms-2">Account verzoek behandelen</p>
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
                           <i wire:click="cancelEditAccountRequest()" class="absolute right-0 fa-solid fa-xmark text-xl hover:cursor-pointer"></i>
                        </div>

                        Account gegevens:
                        <br/><br/>


                        <div class="grid md:grid-cols-2 md:gap-6">
                            <div class="relative z-0 w-full mb-5 group">
                                <label for="gebruikersnaam" class="text-gray-400">Gebruikersnaam</label>
                                <input type="text" wire:model="gebruikersnaam" name="gebruikersnaam" id="gebruikersnaam" class="disabled:hover:cursor-not-allowed block py-2.5 px-0 w-full text-md text-gray-900 border-0 border-b-2 border-gray-300 appearance-none dark:text-gray-900 dark:border-gray-600 focus:outline-none focus:ring-0 focus:border-b-[#C0A16E]" placeholder=" " required />
                                <div class="text-red-500">@error('gebruikersnaam') {{ $message }} @enderror</div>
                            </div>

                            <div class="relative z-0 w-full mb-5 group">
                                <label for="intaker_name" class="text-gray-400">E-mail</label>
                                <input type="text" wire:model="email" name="email" id="email" class=" disabled:hover:cursor-not-allowed block py-2.5 px-0 w-full text-md text-gray-900 border-0 border-b-2 border-gray-300 appearance-none dark:text-gray-900 dark:border-gray-600 focus:outline-none focus:ring-0 focus:border-b-[#C0A16E]" placeholder=" " required />
                                <div class="text-red-500">@error('email') {{ $message }} @enderror</div>
                            </div>
                        </div>

                        <div class="grid md:grid-cols-2 md:gap-6">
                            <div class="relative z-0 w-full mb-5 group">
                                <label for="phone" class="text-gray-400">Telefoonnummer</label>
                                <input type="text" wire:model="phone" name="phone" id="phone" class="disabled:hover:cursor-not-allowed block py-2.5 px-0 w-full text-md text-gray-900 border-0 border-b-2 border-gray-300 appearance-none dark:text-gray-900 dark:border-gray-600 focus:outline-none focus:ring-0 focus:border-b-[#C0A16E]" placeholder=" " required />
                                <div class="text-red-500">@error('phone') {{ $message }} @enderror</div>
                            </div>
                            <div class="relative z-0 w-full mb-5 group">
                                <label for="gebruikersnaam" class="text-gray-400">Ingevulde bedrijfsnaam:</label>
                                <div class="absolute right-0 top-0">
                                    <i wire:click="addCompany()" class="hover:cursor-pointer text-[#C0A16E] text-[20px] fa fa-plus" aria-hidden="true"></i>
                                </div>
                                <input type="text" disabled wire:model="bedrijfsnaam" name="bedrijfsnaam" id="bedrijfsnaam" class="disabled:hover:cursor-not-allowed block py-2.5 px-0 w-full text-md text-gray-900 border-0 border-b-2 border-gray-300 appearance-none dark:text-gray-900 dark:border-gray-600 focus:outline-none focus:ring-0 focus:border-b-[#C0A16E]" placeholder=" " required />
                                <div class="text-red-500">@error('bedrijfsnaam') {{ $message }} @enderror</div>
                            </div>
                        </div>

                        Account verzoek behandelen:
                        <br/><br/>

                        @if(Session::has('success'))
                            <div id="alert-3" class="flex items-center p-4 mb-4 text-green-800 rounded-lg bg-green-50" role="alert">
                                <svg class="shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                                </svg>
                                <span class="sr-only">Info</span>
                                <div class="ms-3 text-sm font-medium">
                                    {{ session('success') }}
                                </div>
                                <button type="button" class="ms-auto -mx-1.5 -my-1.5 bg-green-50 text-green-500 rounded-lg focus:ring-2 focus:ring-green-400 p-1.5 hover:bg-green-200 inline-flex items-center justify-center h-8 w-8" data-dismiss-target="#alert-3" aria-label="Close">
                                    <span class="sr-only">Close</span>
                                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                    </svg>
                                </button>
                            </div>
                        @endif

                        @if(Session::has('error'))
                            <div id="alert-2" class="flex items-center p-4 mb-4 text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
                                <svg class="shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                                </svg>
                                <span class="sr-only">Info</span>
                                <div class="ms-3 text-sm font-medium">
                                    {{ session('error') }}
                                </div>
                                <button type="button" class="ms-auto -mx-1.5 -my-1.5 bg-red-50 text-red-500 rounded-lg focus:ring-2 focus:ring-red-400 p-1.5 hover:bg-red-200 inline-flex items-center justify-center h-8 w-8 dark:bg-gray-800 dark:text-red-400 dark:hover:bg-gray-700" data-dismiss-target="#alert-2" aria-label="Close">
                                    <span class="sr-only">Close</span>
                                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                    </svg>
                                </button>
                            </div>

                        @endif

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
                                <label for="company_id" class="text-gray-400">Wijs gebruiker aan een bestaand bedrijf toe</label>
                                <div class="tooltip">
                                    <div class="tooltip-content">
                                        Als de ingevulde bedrijfsnaam nog niet bestaat in het systeem druk dan op <i class="text-[#C0A16E] text-[20px] fa fa-plus" aria-hidden="true"></i> naast "ingevulde bedrijfsnaam" om het bedrijf toe te voegen in het systeem.
                                    </div>
                                    <i wire:click.prevent="" class="fa-solid fa-circle-info hover:cursor-pointer" id="tooltip"></i>
                                </div>

                                <select id="company_id" wire:model="company_id" class="block py-2.5 px-0 w-full text-sm text-gray-500 bg-transparent border-0 border-b-2 border-gray-200 appearance-none dark:text-gray-900 dark:border-gray-700 focus:outline-none focus:ring-0 focus:border-gray-200 peer">
                                        <option value="0">Geen bedrijf</option>
                                    @foreach($this->companys as $company)
                                        <option value="{{$company->id}}">{{$company->bedrijfsnaam}}</option>
                                    @endforeach
                                </select>
                                <div class="text-red-500">@error('company_id') {{ $message }} @enderror</div>
                            </div>
                            @endadmin
                        </div>

                        <div class="relative z-0 w-full mb-5 group">
                            <label for="architect" class="text-gray-400">Architect</label>
                            <select id="architect" wire:model="architect" class="block py-2.5 px-0 w-full text-sm text-gray-500 bg-transparent border-0 border-b-2 border-gray-200 appearance-none dark:text-gray-900 dark:border-gray-700 focus:outline-none focus:ring-0 focus:border-gray-200 peer">
                                <option value="0" @if($this->architect == 0) selected @endif>Nee</option>
                                <option value="1" @if($this->architect) selected @endif >Ja</option>
                            </select>
                        </div>


                        <button wire:loading.attr="disabled" wire:click.prevent="updateUser({{$this->user->id}})" class="text-white bg-[#C0A16E] mt-10 hover:bg-[#d1b079] disabled:bg-[#c0a16e99] disabled:cursor-not-allowed hover:cursor-pointer focus:outline-none font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center">Gebruiker updaten</button>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

