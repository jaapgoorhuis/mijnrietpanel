
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
                    <p class="ms-1 text-sm font-medium text-gray-700 md:ms-2 dark:text-gray-400">Marketing</p>
                </div>
            </li>
        </ol>
    </nav>
</x-slot>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                @admin
                <div class="text-right">
                    <button wire:click="uploadMarketing()" type="button" class="text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2">
                        <i class="fa-solid fa-upload"></i> Bestanden toevoegen
                    </button>
                </div>
                <br/>
                @endadmin

                <div class="grid grid-cols-1 gap-10 md:grid-cols-2 lg:grid-cols-4 text-left">
                    @if(!count($this->marketing))
                        Er zijn geen bestanden gevonden
                    @else
                        @foreach($this->marketing as $marketing)
                            <div class="relative border-[1px] border-solid border-[#e5e7eb] rounded-[5px] p-5 text-left">
                                <h2 class="text-md font-bold pb-5 break-words whitespace-normal overflow-wrap break-word">{{$marketing->friendly_name}}</h2>
                                <a target="_blank" href="{{asset('/storage/marketing/'.$marketing->file_name)}}">
                                    <button type="button" class="text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2">
                                        <i class="fa-solid fa-download"></i> Downloaden
                                    </button>
                                    <div class="absolute right-[10px] bottom-[10px]">
                                        <input wire:click="updateDownload" wire:model="selectedDownloads" value="{{ $marketing->file_name }}" type="checkbox"/>
                                    </div>
                                </a>
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
