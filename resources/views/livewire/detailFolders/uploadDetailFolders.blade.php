
<x-slot name="header">
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
            <li class="inline-flex items-center">
                <a href="/dashboard" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-[#C0A16E]">
                    {{ __('messages.Mijn Rietpanel') }}
                </a>
            </li>

            <li class="inline-flex items-center">
                <div class="flex items-center">
                <i class="fa-solid fa-angle-right"></i>
                <a href="/detail-maps" class="md:ms-2 inline-flex items-center text-sm font-medium text-gray-700 hover:text-[#C0A16E]">
                    {{ __('messages.Detail categorieën') }}
                </a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fa-solid fa-angle-right"></i>
                    <p class="ms-1 text-sm font-medium text-gray-700 md:ms-2">   {{ __('messages.Map aanmaken') }}</p>
                </div>
            </li>
        </ol>
    </nav>
</x-slot>

<div class="py-12 max-w-9xl mx-auto sm:px-6 lg:px-8">

    {{-- Success / Error Messages --}}
    @if(Session::has('success'))
        <div class="p-4 mb-4 text-green-800 rounded-lg bg-green-50" role="alert">
            {{ session('success') }}
        </div>
    @endif

    @if(Session::has('error'))
        <div class="p-4 mb-4 text-red-800 rounded-lg bg-red-50" role="alert">
            {{ session('error') }}
        </div>
    @endif

    {{-- Nieuwe map aanmaken --}}
    <div class="bg-white p-6 rounded-lg shadow mb-8">
        <h2 class="text-lg font-semibold mb-4">Nieuwe map aanmaken</h2>
        <div class="grid md:grid-cols-3 gap-4">
            <div class="col-span-2">
                <input type="text" wire:model="newFolderTitle" placeholder="Titel van de map"
                       class="block w-full text-gray-900 border border-gray-300 rounded-lg p-2" />
                @error('newFolderTitle') <span class="text-red-500">{{ $message }}</span> @enderror
            </div>

            <div>
                <input type="file" wire:model="newFolderImage" id="folderImageInput" class="block w-full text-gray-900" />
                @error('newFolderImage') <span class="text-red-500">{{ $message }}</span> @enderror

                {{-- Cropping preview --}}
                @if($croppedImage)
                    <img src="{{ $croppedImage }}" class="mt-2 rounded shadow" alt="Preview" />
                @endif
            </div>
        </div>

        <div class="mt-4">
            <button wire:click="createFolder"
                    class="bg-gray-800 text-white px-4 py-2 rounded hover:bg-gray-900">
                Map aanmaken
            </button>
        </div>
    </div>

    {{-- Lijst van mappen --}}
    <div class="bg-white p-6 rounded-lg shadow">
        <h2 class="text-lg font-semibold mb-4">Mappen</h2>


        @if($folders->isEmpty())
            <p>Er zijn nog geen mappen aangemaakt.</p>
        @else
            <ul wire:sortable="updateFoldersOrder" class="space-y-4">
                @foreach($folders as $folder)
                    <li wire:sortable.item="{{ $folder->id }}" wire:key="folder-{{ $folder->id }}"
                        class="border rounded p-4 flex items-center justify-between gap-4 cursor-move bg-white hover:bg-gray-50">

                        <div class="flex items-center gap-4">
                            {{-- Afbeelding --}}
                            @if($folder->cropimage)
                                <img src="{{ asset('storage/'.$folder->cropimage) }}" class="w-16 h-16 rounded object-cover" />
                            @else
                                <div class="w-16 h-16 bg-gray-200 rounded flex items-center justify-center text-gray-400">
                                    Geen afbeelding
                                </div>
                            @endif

                            {{-- Titel bewerken --}}
                            <input type="text" wire:model.defer="editingFolderTitle.{{ $folder->id }}"
                                   class="border border-gray-300 rounded p-1" placeholder="Titel">
                        </div>

                        <div class="flex gap-2">
                            <button wire:click="updateFolderTitle({{ $folder->id }})"
                                    class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">
                                Updaten
                            </button>

                            <button wire:click="deleteFolder({{ $folder->id }})"
                                    class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700">
                                Verwijderen
                            </button>
                        </div>
                    </li>

                    {{-- Details van de geselecteerde map --}}
                    @if($selectedFolder && $selectedFolder->id === $folder->id)
                        <div class="mt-4 border-t pt-4">
                            <livewire:detail-upload :folder="$selectedFolder" />
                        </div>
                    @endif

                @endforeach
            </ul>

        @endif
    </div>
</div>


