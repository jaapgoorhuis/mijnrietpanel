
<x-slot name="header">
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
            <li class="inline-flex items-center">
                <a href="/dashboard" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-[#C0A16E]">
                    Mijn rietpanel
                </a>
            </li>

            <li class="inline-flex items-center">
                <div class="flex items-center">
                    <i class="fa-solid fa-angle-right"></i>
                    <a href="/pricelist" class="md:ms-2 inline-flex items-center text-sm font-medium text-gray-700 hover:text-[#C0A16E]">
                        Prijstlijst / Algemene voorwaarden
                    </a>
                </div>
            </li>

            <li>
                <div class="flex items-center">
                    <i class="fa-solid fa-angle-right"></i>
                    <p class="ms-1 text-sm font-medium text-gray-700 md:ms-2 ">Uploaden</p>
                </div>
            </li>
        </ol>
    </nav>
</x-slot>


<div class="py-12">

    <div class="max-w-8xl mx-auto sm:px-6 lg:px-8">
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
            <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50" role="alert">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                @admin
                <div class="grid md:grid-cols-10">
                    <div class="md:col-span-9">
                        <input wire:model="files" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" id="file_input" type="file" multiple>
                        @error('files.*') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    <div class="md:col-span-1 text-right pt-[10px] md:pt-0">
                        <button wire:loading.attr="disabled" wire:target="files" wire:click="uploadFiles()" type="button" class="disabled:cursor-not-allowed w-[100%] md:w-auto text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-gray-700 dark:border-gray-700">
                            <div wire:loading wire:target="files">
                                <i class="fa-solid fa-spinner fa-spin"></i>
                            </div>
                            <i wire:loading.attr="hidden" wire:target="files" class="fa-solid fa-upload"></i>
                        </button>
                    </div>
                </div>

                @endadmin
                <br/>
                <br/>
                <div class="grid">

                    @if(!count($this->pricelist))
                        Er zijn geen bestanden gevonden
                    @else
                        <div id="accordion-collapse" data-accordion="collapse">
                            <ul wire:sortable="updatePricelistOrder">
                                @foreach($this->pricelist as $key => $pricelist)
                                    <li wire:sortable.item="{{$pricelist->id}}" wire:key="{{$key}}" >
                                        <div class="grid grid-cols-10">
                                            <div class="col-span-1 text-center pt-[15px]">
                                                <i wire:sortable.handle class="fa-solid fa-sort hover:cursor-pointer"></i>
                                            </div>
                                            <div class="col-span-9">
                                                <h2 id="accordion-{{$key}}-heading">
                                                    <button type="button" class="flex items-center justify-between w-full p-5 font-medium rtl:text-right text-gray-500 border border-gray-200 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-800 dark:border-gray-700 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 gap-3" data-accordion-target="#accordion-{{$key}}-body" aria-expanded="false" aria-controls="accordion-{{$key}}-body">
                                                            <span class="flex items-center">
                                                                {{$pricelist->friendly_name}}
                                                            </span>
                                                        <svg data-accordion-icon class="w-3 h-3 rotate-180 shrink-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5 5 1 1 5"/>
                                                        </svg>
                                                    </button>
                                                </h2>
                                                <div id="accordion-{{$key}}-body" class="hidden" aria-labelledby="accordion-{{$key}}-heading">
                                                    <div class="p-5 border border-gray-200 dark:border-gray-700">
                                                        <div class="relative z-0 w-full mb-5 group">
                                                            <div class="text-right">
                                                                <i wire:click="removePricelist({{$pricelist->id}})" class="fa-solid fa-trash hover:cursor-pointer"></i>
                                                            </div>
                                                            <label for="project_name" class="text-gray-400">Bestandsnaam</label>
                                                            <input placeholder="{{$pricelist->friendly_name}}" type="text" wire:model="friendly_name.{{$pricelist->id}}"  name="friendly_name.{{$pricelist->id}}" id="friendly_name.{{$pricelist->id}}" class="block py-2.5 px-0 w-full text-md text-gray-900 border-0 border-b-2 border-gray-300 appearance-none dark:text-gray-900 dark:border-gray-600 focus:outline-none focus:ring-0 focus:border-b-[#C0A16E]"  required />
                                                            <div class="text-red-500">@error('friendly_name.'.$pricelist->id) {{ $message }} @enderror</div>

                                                            <div class="text-right">
                                                                <br/>
                                                                <button wire:click="updateFileName({{$pricelist->id}})" type="button" class="disabled:cursor-not-allowed w-[100%] md:w-auto text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-gray-700 dark:border-gray-700">
                                                                    Updaten
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>
