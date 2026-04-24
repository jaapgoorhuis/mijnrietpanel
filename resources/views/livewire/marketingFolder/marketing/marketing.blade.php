
<x-slot name="header">
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
            <li class="inline-flex items-center">
                <a href="/dashboard" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-[#C0A16E]">
                    {{ __('messages.Mijn Rietpanel') }}
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fa-solid fa-angle-right"></i>
                    <a href="/marketing-maps" class="ms-1 text-sm font-medium text-gray-700 md:ms-2 hover:text-[#C0A16E]">
                        {{ __('messages.Marketing categorieën') }}
                    </a>
                </div>
            </li>

            <li>
                <div class="flex items-center">
                    <i class="fa-solid fa-angle-right"></i>
                    <p class="ms-1 text-sm font-medium text-gray-700 md:ms-2 ">  {{$folder->name}}</p>
                </div>
            </li>
        </ol>
    </nav>
</x-slot>

<div class="py-12">
    <div class="max-w-9xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 relative">
                <div class="flex flex-col gap-3 md:flex-row md:justify-between">

                    @admin
                    <div class="text-right md:text-left">
                        <button wire:click="uploadMarketing()" type="button"
                                class="w-full md:w-auto text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5">
                            <i class="fa-solid fa-upload"></i>
                            {{ __('messages.Bestanden toevoegen') }}
                        </button>
                    </div>
                    @endadmin

                    <div class="text-left md:text-right">
                        <button
                            wire:click="downloadAll()"
                            wire:loading.attr="disabled"
                            wire:target="downloadAll"
                            type="button"
                            class="w-full md:w-auto disabled:cursor-not-allowed text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5"
                        >
            <span wire:loading wire:target="downloadAll">
                <i class="fa-solid fa-spinner fa-spin me-2"></i>
            </span>

                            <i wire:loading.remove wire:target="downloadAll" class="fa-solid fa-download me-2"></i>
                            {{ __('messages.Alle bestanden downloaden') }}
                        </button>
                    </div>

                </div>
                <br/>
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4 text-left">
                    @if(!count($this->marketing))
                        {{ __('messages.Er zijn geen bestanden gevonden') }}
                    @else
                        @foreach($this->marketing as $marketing)
                            <div class="relative border border-gray-200 rounded-md p-3 flex flex-col items-center text-sm
                        {{ !$marketing->cropimage ? 'justify-center h-40' : '' }}">

                                {{-- Cropimage compact --}}
                                @if($marketing->cropimage)
                                    <div class="w-full h-24 mb-2 overflow-hidden rounded-md ">
                                        <img src="{{ asset('storage/marketing/' . $marketing->cropimage) }}"
                                             alt="{{ $marketing->cropimage }}"
                                             class="w-full h-full object-contain">
                                    </div>
                                @endif

                                {{-- Bestandsnaam --}}
                                <h2 class="font-semibold pb-2 text-center truncate w-full">
                                    {{ $marketing->friendly_name }}
                                </h2>

                                {{-- Download knop klein --}}
                                <a target="_blank" href="{{ asset('/storage/marketing/' . $marketing->file_name) }}" class="w-full mb-[20px]">
                                    <button type="button"
                                            class="w-full bg-gray-800 hover:bg-gray-900 text-white rounded-md text-sm px-3 py-1.5 flex items-center justify-center gap-1">
                                        <i class="fa-solid fa-download text-[0.7rem]"></i>
                                        {{ __('messages.Downloaden') }}
                                    </button>
                                </a>

                                {{-- Checkbox klein --}}
                                <div class="absolute right-2 bottom-2">
                                    <input wire:click="updateDownload"
                                           wire:model="selectedDownloads"
                                           value="{{ $marketing->file_name }}"
                                           type="checkbox"/>
                                </div>

                            </div>
                        @endforeach
                    @endif
                </div>

            @if($this->selectedDownloads)
                    <div class="fixed bottom-[20px] w-full pr-[49px]">
                        <button wire:click="downloadSelected()" type="button"
                                class="mt-[20px] w-full md:w-auto text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2">
                            <i class="fa-solid fa-download"></i>    {{ __('messages.Geselecteerde bestanden downloaden') }}
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
