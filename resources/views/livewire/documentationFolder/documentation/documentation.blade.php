
<x-slot name="header">
    <nav class="w-full overflow-x-auto" aria-label="Breadcrumb">
        <ol class="flex items-center gap-2 whitespace-nowrap text-sm text-gray-700">

            <li class="flex items-center gap-2">
                <a href="/dashboard" class="hover:text-[#C0A16E]">
                    {{ __('messages.Mijn Rietpanel') }}
                </a>
            </li>

            <li class="flex items-center gap-2">
                <i class="fa-solid fa-angle-right text-gray-400"></i>
                <a href="/detail-maps" class="hover:text-[#C0A16E]">
                    {{ __('messages.Documentatie categorieën') }}
                </a>
            </li>

            <li class="flex items-center gap-2">
                <i class="fa-solid fa-angle-right text-gray-400"></i>
                <span class="text-gray-900 font-medium truncate max-w-[160px]">
                {{$folder->name}}
            </span>
            </li>

        </ol>
    </nav>
</x-slot>

<div class="py-12">

    <div class="max-w-9xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 relative">
                <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">

                    @admin
                    <button wire:click="uploadDocumentation()" type="button"
                            class="w-full md:w-auto text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5">
                        <i class="fa-solid fa-upload me-2"></i>
                        {{ __('messages.Bestanden toevoegen') }}
                    </button>
                    @endadmin

                    @if(count($documentation))
                        <button
                            wire:click="downloadAll()"
                            wire:loading.attr="disabled"
                            wire:target="downloadAll"
                            type="button"
                            class="w-full md:w-auto disabled:cursor-not-allowed text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5"
                        >
                            <!-- Spinner -->
                            <span wire:loading wire:target="downloadAll">
                <i class="fa-solid fa-spinner fa-spin me-2"></i>
            </span>

                            <!-- Normale icon -->
                            <span wire:loading.remove wire:target="downloadAll">
                <i class="fa-solid fa-download me-2"></i>
            </span>

                            {{ __('messages.Alle bestanden downloaden') }}
                        </button>
                    @endif

                </div>
                <br/>


                <div class="grid grid-cols-1 gap-10 md:grid-cols-2 lg:grid-cols-4 text-left">
                    @if(!count($this->documentation))
                        {{ __('messages.Er zijn geen bestanden gevonden') }}
                    @else
                        @foreach($this->documentation as $documentation)
                            <div class="relative border-[1px] border-solid border-[#e5e7eb] rounded-[5px] p-5 text-left">
                                <h2 class="text-md font-bold pb-5 break-words whitespace-normal overflow-wrap break-word">{{$documentation->friendly_name}}</h2>
                                <a target="_blank" href="{{asset('/storage/documentation/'.$documentation->file_name)}}">
                                    <button type="button" class="text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2">
                                        <i class="fa-solid fa-download"></i> Downloaden
                                    </button>
                                    <div class="absolute right-[10px] bottom-[10px]">
                                        <input wire:click="updateDownload" wire:model="selectedDownloads" value="{{ $documentation->file_name }}" type="checkbox"/>
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

