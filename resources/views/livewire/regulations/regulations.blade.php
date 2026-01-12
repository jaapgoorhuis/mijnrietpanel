
<x-slot name="header">
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
            <li class="inline-flex items-center">
                <a href="/dashboard" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-[#C0A16E] ">
                    Mijn rietpanel
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fa-solid fa-angle-right"></i>
                    <p class="ms-1 text-sm font-medium text-gray-700 md:ms-2 ">Voorschriften</p>
                </div>
            </li>
        </ol>
    </nav>
</x-slot>

<div class="py-12">
    <div class="max-w-8xl mx-auto sm:px-6 lg:px-8">
        <div class="max-w-8xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @admin
                    <div class="text-right">
                        <button wire:click="uploadRegulation()" type="button" class="w-full sm:w-auto text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2">
                             <i class="fa-solid fa-upload"></i> Bestanden toevoegen
                        </button>
                    </div>
                    <br/>
                    @endadmin

                    <div class="grid grid-cols-1 gap-10 md:grid-cols-4 text-left">
                        @if(!count($this->regulations))
                            Er zijn geen bestanden gevonden
                        @else
                            @foreach($this->regulations as $regulation)
                                <div class="border-[1px] border-solid border-[#e5e7eb] rounded-[5px] p-5 text-left">
                                   <h2 class="text-md font-bold pb-5">{{$regulation->friendly_name}}</h2>
                                    <a target="_blank" href="{{asset('/storage/regulations/'.$regulation->file_name)}}">
                                        <button type="button" class="text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2">
                                            <i class="fa-solid fa-download"></i> Downloaden
                                        </button>
                                    </a>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
