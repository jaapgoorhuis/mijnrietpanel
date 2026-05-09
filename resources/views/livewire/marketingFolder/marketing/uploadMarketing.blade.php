<x-slot name="header">
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">

            <li>
                <a href="/dashboard" class="text-sm text-gray-700 hover:text-[#C0A16E]">
                    {{ __('messages.Mijn Rietpanel') }}
                </a>
            </li>

            <li><i class="fa-solid fa-angle-right"></i></li>

            <li>
                <a href="/marketing-maps" class="text-sm text-gray-700 hover:text-[#C0A16E]">
                    {{ __('messages.Marketing categorieën') }}
                </a>
            </li>

            <li><i class="fa-solid fa-angle-right"></i></li>

            <li>
                <a href="/marketing-maps/{{$folderId}}/marketing"
                   class="text-sm text-gray-700 hover:text-[#C0A16E]">
                    {{$folder->name}}
                </a>
            </li>

            <li><i class="fa-solid fa-angle-right"></i></li>

            <li class="text-sm text-gray-700">
                {!! __('messages.uploaden') !!}
            </li>

        </ol>
    </nav>
</x-slot>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        {{-- FLASH --}}
        @if(session()->has('success'))
            <div class="p-4 mb-4 bg-green-100 text-green-800 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if(session()->has('error'))
            <div class="p-4 mb-4 bg-red-100 text-red-800 rounded">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white shadow-sm rounded-lg p-6">

            {{-- UPLOAD --}}
            @admin
            <div
                x-data="{ uploading: false }"
                x-on:livewire-upload-start="uploading = true"
                x-on:livewire-upload-finish="uploading = false"
                x-on:livewire-upload-error="uploading = false"
                class="bg-white p-6 rounded-lg shadow mb-6"
            >

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                    {{-- FILE --}}
                    <div>
                        <label class="text-sm font-medium text-gray-700">📄 Bestand</label>

                        <input type="file"
                               wire:model="file"
                               class="w-full border rounded-lg p-2 bg-gray-50">

                        @error('file')
                        <div class="text-red-500 text-sm">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- THUMBNAIL --}}
                    <div>
                        <label class="text-sm font-medium text-gray-700">🖼 Thumbnail</label>

                        <input type="file"
                               wire:model="newCropimage"
                               class="w-full border rounded-lg p-2 bg-gray-50">

                        @error('newCropimage')
                        <div class="text-red-500 text-sm">{{ $message }}</div>
                        @enderror

                        {{-- OPTIONELE PREVIEW (NIET BLOCKING) --}}
                        @if ($newCropimage && str_starts_with($newCropimage->getMimeType(), 'image/'))
                            <img src="{{ $newCropimage->temporaryUrl() }}"
                                 class="h-20 mt-2 border rounded object-contain">
                        @endif
                    </div>

                    {{-- BUTTON --}}
                    <div class="flex items-end">

                        <button
                            type="button"
                            wire:click="uploadFiles"
                            wire:loading.attr="disabled"
                            wire:target="uploadFiles,file,newCropimage"
                            :disabled="uploading || !$wire.file || !$wire.newCropimage"
                            class="w-full bg-gray-800 hover:bg-gray-900 text-white rounded-lg px-4 py-3
                               flex items-center justify-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed"
                        >

                        <span x-show="!uploading" wire:loading.remove wire:target="uploadFiles">
                            <i class="fa-solid fa-upload"></i> Uploaden
                        </span>

                            <span x-show="uploading || $wire.__instance?.loading">
                            <i class="fa-solid fa-spinner fa-spin"></i> Uploaden...
                        </span>

                        </button>

                    </div>

                </div>
            </div>
            @endadmin

            {{-- LIST --}}
            @if(!count($this->marketing))
                <p class="text-gray-500">Geen bestanden gevonden</p>
            @else

                <ul wire:sortable="updateOrder">

                    @foreach($this->marketing as $marketing)

                        <li wire:sortable.item="{{ $marketing->id }}"
                            wire:key="marketing-{{ $marketing->id }}">

                            <div x-data="{ open: false }" class="border rounded-lg mb-3">

                                {{-- HEADER --}}
                                <div @click="open = !open"
                                     class="flex justify-between p-4 bg-gray-50 cursor-pointer">

                                    <div class="flex items-center gap-3">
                                        <i wire:sortable.handle class="fa-solid fa-grip cursor-move"></i>
                                        <span>{{ $marketing->friendly_name }}</span>
                                    </div>

                                    <i :class="open ? 'fa-chevron-up' : 'fa-chevron-down'" class="fa-solid"></i>
                                </div>

                                {{-- CONTENT --}}
                                <div x-show="open" x-transition class="p-5 border-t">

                                    <button wire:click="remove({{ $marketing->id }})"
                                            class="text-red-500 text-sm mb-3">
                                        Verwijderen
                                    </button>

                                    {{-- NAME --}}
                                    <input type="text"
                                           wire:model="friendly_name.{{ $marketing->id }}"
                                           class="w-full border-b mb-4">

                                    {{-- UPLOAD --}}
                                    <input type="file"
                                           wire:model="cropimage.{{ $marketing->id }}"
                                           class="w-full border rounded p-2">

                                    @error('cropimage.'.$marketing->id)
                                    <div class="text-red-500 text-sm">{{ $message }}</div>
                                    @enderror

                                    {{-- CURRENT --}}
                                    @if($marketing->cropimage)
                                        <img src="{{ asset('storage/marketing/' . $marketing->cropimage) }}"
                                             class="h-24 mt-3 border rounded object-contain">
                                    @endif

                                    {{-- TEMP PREVIEW --}}
                                    @if(isset($cropimage[$marketing->id]))
                                        <img src="{{ $cropimage[$marketing->id]->temporaryUrl() }}"
                                             class="h-24 mt-3 border rounded object-contain">
                                    @endif

                                    {{-- SAVE --}}
                                    <button wire:click="updateItem({{ $marketing->id }})"
                                            class="mt-4 bg-gray-800 text-white px-4 py-2 rounded">
                                        Opslaan
                                    </button>

                                </div>
                            </div>

                        </li>

                    @endforeach

                </ul>

            @endif

        </div>
    </div>
</div>
