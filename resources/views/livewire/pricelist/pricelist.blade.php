
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
                    <p class="ms-1 text-sm font-medium text-gray-700 md:ms-2">Prijslijst / Algemene voorwaarden</p>
                </div>
            </li>
        </ol>
    </nav>
</x-slot>

<div class="py-12">

    <div class="max-w-8xl mx-auto sm:px-6 lg:px-8">
        @if(Session::has('error'))
            <div id="alert-2" class="flex items-center p-4 mb-4 text-red-800 rounded-lg bg-red-50 " role="alert">
                <svg class="shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                </svg>
                <span class="sr-only">Info</span>
                <div class="ms-3 text-sm font-medium">
                    {{ session('error') }}
                </div>
                <button type="button" class="ms-auto -mx-1.5 -my-1.5 bg-red-50 text-red-500 rounded-lg focus:ring-2 focus:ring-red-400 p-1.5 hover:bg-red-200 inline-flex items-center justify-center h-8 w-8 " data-dismiss-target="#alert-2" aria-label="Close">
                    <span class="sr-only">Close</span>
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                </button>
            </div>

        @endif
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 relative">
                @admin
                <div class="text-right">
                    <button wire:click="uploadPricelist()" type="button" class="text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2">
                        <i class="fa-solid fa-upload"></i> Bestanden toevoegen
                    </button>
                </div>
                <br/>
                @endadmin

                <div class="text-left">
                    <button
                        wire:click="downloadAll()"
                        wire:loading.attr="disabled"
                        wire:target="downloadAll"
                        type="button"
                        class="absolute top-[25px] disabled:cursor-not-allowed text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2"
                    >
                        <!-- Spinner verschijnt alleen tijdens downloadAll() -->
                        <span wire:loading wire:target="downloadAll">
                            <i class="fa-solid fa-spinner fa-spin me-2"></i>
                        </span>

                        <!-- Download icon en tekst normaal -->
                        <i wire:loading.remove wire:target="downloadAll" class="fa-solid fa-download me-2"></i>
                        Alle bestanden downloaden
                    </button>
                </div>
                <br/>
                <div class="grid grid-cols-1 gap-10 md:grid-cols-2 lg:grid-cols-4 text-left">
                    @if(!count($this->pricelist))
                        Er zijn geen bestanden gevonden
                    @else
                        @foreach($this->pricelist as $key => $pricelist)
                            <div wire:key="{{$pricelist->id}}" class="relative border-[1px] border-solid border-[#e5e7eb] rounded-[5px] p-5 text-left">
                                <h2 class="text-md font-bold pb-5 break-words whitespace-normal overflow-wrap break-word">{{$pricelist->friendly_name}}</h2>
                                <a target="_blank" href="{{asset('/storage/pricelist/'.$pricelist->file_name)}}">
                                    <button type="button" class="text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2">
                                        <i class="fa-solid fa-download"></i> Downloaden
                                    </button>
                                </a>
                                    <div class="absolute right-[10px] bottom-[10px]">
                                        <input wire:click="updateDownload" wire:model="selectedDownloads" value="{{ $pricelist->file_name }}" type="checkbox"/>
                                    </div>

                            </div>
                        @endforeach
                    @endif
                </div>
                @if($this->selectedDownloads)
                    <div class="fixed bottom-[20px] w-full pr-[49px]">
                        <button wire:click="downloadSelected()" type="button"
                                class="mt-[20px] w-full md:w-auto text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2">
                            <i class="fa-solid fa-download"></i> Geselecteerde bestanden downloaden
                        </button>
                    </div>
                @endif

            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.addEventListener("livewire:initialized", () => {
            if (typeof Livewire !== 'undefined') {
                Livewire.on('download-zip', ({ url }) => {
                    const link = document.createElement('a');
                    link.href = url;
                    link.setAttribute('download', '');
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                });
            } else {
                console.error('Livewire is not defined');
            }
        });
    });
</script>

