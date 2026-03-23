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
                    <a href="/documentation-maps" class="ms-1 text-sm font-medium text-gray-700 md:ms-2 hover:text-[#C0A16E]">
                        {{ __('messages.Documentatie categorieën') }}
                    </a>
                </div>
            </li>

            <li>
                <div class="flex items-center">
                    <i class="fa-solid fa-angle-right"></i>
                    <a href="/documentation-maps/{{$folderId}}/documentation" class="ms-1 text-sm font-medium text-gray-700 md:ms-2 hover:text-[#C0A16E]">
                        {{$folder->name}}
                    </a>
                </div>
            </li>

            <li>
                <div class="flex items-center">
                    <i class="fa-solid fa-angle-right"></i>
                    <p class="ms-1 text-sm font-medium text-gray-700 md:ms-2 ">  {!! __('messages.uploaden') !!}</p>
                </div>
            </li>
        </ol>
    </nav>
</x-slot>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        {{-- SUCCESS --}}
        @if(session()->has('success'))
            <div class="p-4 mb-4 bg-green-100 text-green-800 rounded">
                {{ session('success') }}
            </div>
        @endif

        {{-- ERROR --}}
        @if(session()->has('error'))
            <div class="p-4 mb-4 bg-red-100 text-red-800 rounded">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white shadow-sm rounded-lg p-6">

            {{-- UPLOAD NIEUWE BESTANDEN --}}
            @admin
            <div class="bg-white p-6 rounded-lg shadow mb-6">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-start">

                    {{-- 📄 Bestanden upload --}}
                    <div class="flex flex-col">
                        <label class="text-sm font-medium text-gray-700 mb-1">
                            📄 Bestand
                        </label>
                        <input wire:model="files" type="file"
                               class="w-full border border-gray-300 rounded-lg p-2 bg-gray-50 text-sm">
                        <p class="text-xs text-gray-400 mt-1">
                            Upload een bestand
                        </p>
                    </div>

                    {{-- 🖼 Thumbnail upload --}}
{{--                    <div class="flex flex-col">--}}
{{--                        <label class="text-sm font-medium text-gray-700 mb-1">--}}
{{--                            🖼 Thumbnail *--}}
{{--                        </label>--}}
{{--                        <input type="file" wire:model="newCropimage"--}}
{{--                               class="w-full border border-gray-300 rounded-lg p-2 bg-gray-50 text-sm">--}}

{{--                        <p class="text-xs text-gray-400 mt-1">--}}
{{--                            Wordt gebruikt als preview afbeelding *--}}
{{--                        </p>--}}

                        {{-- Preview --}}
{{--                        @if ($newCropimage)--}}
{{--                            <div class="mt-2">--}}
{{--                                <img src="{{ $newCropimage->temporaryUrl() }}"--}}
{{--                                     class="h-20 w-full object-contain border rounded">--}}
{{--                            </div>--}}
{{--                        @endif--}}
{{--                    </div>--}}

                    {{-- Upload knop --}}
                    <div class="flex items-end md:items-center">
                        <button wire:click="uploadFiles"
                                wire:loading.attr="disabled"
                                class="w-full md:w-100 mt-[30px] bg-gray-800 hover:bg-gray-900 text-white rounded-lg px-4 py-3 flex items-center justify-center gap-2">

                <span wire:loading.remove wire:target="newCropimage">
                    <i class="fa-solid fa-upload"></i> Uploaden
                </span>

                            <span wire:loading wire:target="newCropimage">
                    <i class="fa-solid fa-spinner fa-spin"></i> Uploaden...
                </span>

                        </button>
                    </div>

                </div>

            </div>


            @endadmin

            {{-- GEEN ITEMS --}}
            @if(!count($this->documentation))
                <p class="text-gray-500">Geen bestanden gevonden</p>
            @else

                <ul wire:sortable="updateOrder">

                    @foreach($this->documentation as $documentation)

                        <li wire:sortable.item="{{ $documentation->id }}"
                            wire:key="documentation-{{ $documentation->id }}">

                            <div x-data="{ open: false }"
                                 class="border rounded-lg mb-3">

                                {{-- HEADER --}}
                                <div @click="open = !open"
                                     class="flex justify-between items-center p-4 bg-gray-50 cursor-pointer">

                                    <div class="flex items-center gap-3">
                                        <i wire:sortable.handle class="fa-solid fa-sort cursor-move"></i>
                                        <span>{{ $documentation->friendly_name }}</span>
                                    </div>

                                    <i :class="open ? 'fa-solid fa-chevron-up' : 'fa-solid fa-chevron-down'"></i>
                                </div>

                                {{-- CONTENT --}}
                                <div x-show="open" x-transition class="p-5 border-t">

                                    {{-- DELETE --}}
                                    <div class="text-right mb-3">
                                        <i wire:click="remove({{ $documentation->id }})"
                                           class="fa-solid fa-trash cursor-pointer text-red-500"></i>
                                    </div>

                                    {{-- NAAM --}}
                                    <label class="text-gray-400 text-sm">Bestandsnaam</label>
                                    <input type="text"
                                           wire:model="friendly_name.{{ $documentation->id }}"
                                           class="w-full border-b-2 border-gray-300 focus:border-[#C0A16E] mb-4">

{{--                                    --}}{{----}}{{-- IMAGE UPLOAD --}}
{{--                                    <label class="text-gray-400 text-sm">Afbeelding</label>--}}
{{--                                    <input type="file"--}}
{{--                                           wire:model="cropimage.{{ $documentation->id }}"--}}
{{--                                           class="w-full border rounded-lg p-2 bg-gray-50">--}}

{{--                                    --}}{{----}}{{-- ERROR --}}
{{--                                    @error('cropimage.'.$documentation->id)--}}
{{--                                    <div class="text-red-500 text-sm">{{ $message }}</div>--}}
{{--                                    @enderror--}}

{{--                                    --}}{{----}}{{-- HUIDIGE AFBEELDING --}}
{{--                                    @if($documentation->cropimage)--}}
{{--                                        <div class="mt-3">--}}
{{--                                            <p class="text-xs text-gray-400">Huidig:</p>--}}
{{--                                            <img src="{{ asset('storage/' . $documentation->cropimage) }}"--}}
{{--                                                 class="h-24 object-contain border rounded">--}}
{{--                                        </div>--}}
{{--                                    @endif--}}

{{--                                    --}}{{-- NIEUWE PREVIEW --}}
{{--                                    @if(isset($cropimage[$documentation->id]))--}}
{{--                                        <div class="mt-3">--}}
{{--                                            <p class="text-xs text-gray-400">Preview:</p>--}}
{{--                                            <img src="{{ $cropimage[$documentation->id]->temporaryUrl() }}"--}}
{{--                                                 class="h-24 object-contain border rounded">--}}
{{--                                        </div>--}}
{{--                                    @endif--}}

                                    {{-- BUTTON --}}

                                    <div class="text-right mt-4">
                                        <button wire:click="updateItem({{ $documentation->id }})"
                                                wire:loading.attr="disabled"
                                                class="bg-gray-800 text-white px-4 py-2 rounded-lg">
                                            Opslaan
                                        </button>
                                    </div>

                                </div>
                            </div>
                        </li>

                    @endforeach

                </ul>

            @endif

        </div>
    </div>
</div>
