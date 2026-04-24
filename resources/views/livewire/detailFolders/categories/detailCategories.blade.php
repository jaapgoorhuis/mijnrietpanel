
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
                    <a href="/detail-maps" class="ms-1 text-sm font-medium text-gray-700 md:ms-2 hover:text-[#C0A16E]">
                        {{ __('messages.Detail categorieën') }}
                    </a>
                </div>
            </li>

            <li>
                <div class="flex items-center">
                    <i class="fa-solid fa-angle-right"></i>
                    <p class="ms-1 text-sm font-medium text-gray-700 md:ms-2 ">{{$this->folder->name}}</p>
                </div>
            </li>


        </ol>
    </nav>
</x-slot>

<div class="py-12 max-w-9xl mx-auto sm:px-6 lg:px-8">

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">

            <div class="flex items-center justify-between mb-6">
                {{-- Titel altijd zichtbaar --}}
                <h1 class="text-lg font-semibold text-gray-800">
                    {{ __('messages.Details') }}
                </h1>

                {{-- Knop alleen voor admins --}}
                @admin
                <button wire:click="uploadDetailCategory()" type="button"
                        class="text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 flex items-center gap-2">
                    <i class="fa-solid fa-upload"></i> {{ __('messages.Mappen toevoegen') }}
                </button>
                @endadmin
            </div>

            @if($detailCategories->isEmpty())
                <p class="text-gray-500">{{ __('messages.Er zijn geen mappen gevonden') }}</p>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-8 gap-4 justify-items-center">
                    @foreach($detailCategories as $category)
                        <a href="{{ url('/detail-maps/'.$this->folderId.'/categories/' . $category->id . '/details') }}"
                           class="group w-48 border rounded-lg overflow-hidden shadow hover:shadow-lg transition duration-200 bg-white flex flex-col items-center p-3">

                            {{-- Afbeelding bovenaan --}}
                            @if($category->cropimage)
                                <div class="w-full h-28 flex items-center justify-center bg-white rounded-md overflow-hidden">
                                    <img src="{{ asset('storage/' . $category->cropimage) }}"
                                         alt="{{ $category->name }}"
                                         class="max-h-full max-w-full object-contain">
                                </div>
                            @else
                                <div class="w-full h-28 bg-gray-200 rounded-md flex items-center justify-center text-gray-400 text-xs">
                                    Geen afbeelding
                                </div>
                            @endif

                            {{-- Titel onder afbeelding --}}
                            <div class="mt-3 text-center w-full">
                                <h2 class="text-sm font-medium text-gray-800 truncate">
                                    {{ $category->name }}
                                </h2>
                            </div>
                        </a>
                    @endforeach
                </div>


            @endif

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

