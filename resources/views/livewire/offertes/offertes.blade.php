
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
                    <p class="ms-1 text-sm font-medium text-gray-700 md:ms-2">{{ __('messages.Offertes') }}</p>
                </div>
            </li>
        </ol>
    </nav>
</x-slot>


<div class="py-12">

    <div class="max-w-9xl mx-auto sm:px-6 lg:px-8">
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
            <div class="p-6 text-gray-900">

                <div class="relative">
                    <button type="button" class="w-full ] mb-[20px] sm:w-auto text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2">
                        <a target="_blank" href="{{asset('/storage/uploads/rietpanel-order-formulier.pdf')}}"> <i class="fa-solid fa-download"></i> {{ __('messages.Download inmeet formulier') }}

                        </a>
                    </button>
                    <div class="relative md:absolute right-0 top-[-9px]">
                        @admin
                        <button wire:click="uploadOrderForm()" type="button" class="w-full sm:w-auto text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5  ">
                            <i class="fa-solid fa-upload"></i> {{ __('messages.Inmeetformulier uploaden') }}
                        </button>
                        @endadmin
                        <button wire:click="newOfferte()" type="button" class="w-full sm:w-auto mt-[10px] text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5  ">
                            <i class="fa fa-plus hover:cursor-pointer"></i> {{ __('messages.Offerte aanmaken') }}
                        </button>
                    </div>

                </div>

                <br/>

                <div wire:ignore class="relative">
                    <table id="offerte-table"  class=" w-full text-sm text-left text-gray-500 ">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 ">
                        <tr>
                            <th scope="col" class="px-4 py-3">Offerte ID</th>
                            <th scope="col" class="px-4 py-3"> {{ __('messages.Klant naam') }}</th>
                            <th scope="col" class="px-4 py-3"> {{ __('messages.Geplaatst door') }}</th>
                            <th scope="col" class="px-4 py-3"> {{ __('messages.Bedrijfsnaam') }}</th>
                            <th scope="col" class="px-4 py-3"> {{ __('messages.Gewenste leverdatum') }}</th>
                            <th scope="col" class="px-4 py-3"> {{ __('messages.Aantal') }} m²</th>
                            @admin
                            <th scope="col" class="px-4 py-3"> {{ __('messages.Land') }}</th>
                            @endadmin
                            <th scope="col" class="px-4 py-3">{{ __('messages.Omgezet tot order') }}</th>
                            <th scope="col" class="px-4 py-3 text-right">
                                <span> {{ __('messages.acties') }}</span>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($this->offertes as $offerte)
                            <tr class="border-b ">
                                <th scope="row" class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap">{{$offerte->offerte_id}}</th>
                                <td class="px-4 py-3">{{$offerte->klantnaam}}</td>
                                <td class="px-4 py-3">{{$offerte->intaker}}</td>
                                <td class="px-4 py-3">
                                    @if($offerte->user->company)
                                        {{$offerte->user->company->bedrijfsnaam}}
                                    @else
                                        Gebruiker is verwijderd
                                    @endif
                                </td>
                                <td class="px-4 py-3">@if($offerte->requested_delivery_date) {{$offerte->requested_delivery_date}} @else Geen datum @endif</td>
                                <td class="px-4 py-3">
                                        <?php $totalM2 = 0?>
                                    @foreach($offerte->offerteLines as $offerteLine)
                                            <?php $totalM2 += $offerteLine->m2;?>
                                    @endforeach
                                    {{$totalM2}} m²
                                </td>
                                @admin

                                <td class="px-4 py-3">

                                    @if($offerte->lang == 'nl')
                                        NL
                                    @else
                                        {{ __('messages.Buitenland')}}
                                    @endif
                                </td>
                                @endadmin
                                <td class="px-4 py-3">
                                    @if($offerte->is_order)
                                        <span> {{ __('messages.Ja') }}</span>
                                    @else
                                        <span> {{ __('messages.Nee') }}</span>
                                    @endif
                                </td>

                                <td  class="px-4 py-3 flex items-center justify-end">
                                    <button wire:ignore.self id="{{$offerte->id}}-dropdown-button" data-dropdown-toggle="{{$offerte->id}}-dropdown" class="inline-flex items-center p-0.5 text-sm font-medium text-center text-gray-500 hover:text-gray-800 rounded-lg focus:outline-none " type="button">
                                        <svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z" />
                                        </svg>
                                    </button>
                                    <div wire:ignore.self id="{{$offerte->id}}-dropdown" class="hidden z-10 w-auto bg-white rounded divide-y divide-gray-100 shadow block " style="position: absolute; inset: 0px auto auto 0px; margin: 0px; transform: translate(1412px, 425px);" data-popper-placement="bottom">
                                        <ul class="py-1 text-sm text-gray-700 " aria-labelledby="{{$offerte->id}}-dropdown-button">

                                            <li>
                                                <a class="block py-2 px-4 hover:bg-gray-100" href="{{asset('/download-offerte/offerte-'.$offerte->offerte_id)}}" target="_blank">
                                                    <i class="fa-solid fa-download"></i> {{ __('messages.Offerte downloaden') }}
                                                </a>
                                            </li>

                                            <li>
                                                <button class="block py-2  px-4 text-left w-full hover:bg-gray-100 disabled:cursor-not-allowed disabled:text-[#16a34a54]" wire:click="createOfferteOrder({{$offerte->id}})" @if($offerte->is_order)disabled @endif>
                                                    <i class="fa-solid fa-circle-check" ></i> {{ __('messages.Order maken') }}
                                                </button>
                                            </li>

                                            <li>
                                                <button @if($offerte->is_order == 1) disabled  @endif class="disabled:cursor-not-allowed disabled:text-[#00000038] block py-2  px-4 text-left w-full hover:bg-gray-100" wire:click="changeOfferte({{$offerte->id}})">
                                                    <i class="fas fa-edit"></i> {{ __('messages.Offerte bewerken') }}
                                                </button>
                                            </li>

                                            <li>
                                                <button @user @if($offerte->is_order == 1) disabled  @endif @enduser class="disabled:cursor-not-allowed disabled:text-[#00000038] block py-2  px-4 text-left w-full hover:bg-gray-100" wire:click="removeOfferte({{$offerte->id}})">
                                                    <i class="fa-solid fa-circle-check" ></i> {{ __('messages.Offerte verwijderen') }}
                                                </button>
                                            </li>

                                        </ul>

                                    </div>
                                </td>
                            </tr>
                        @endforeach

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('open-new-tab', function (event) {
        window.open(event.detail[0].url, '_blank');
    });

    new DataTable("#offerte-table", {

        language: {
            "info": "_START_ tot _END_ van _TOTAL_ resultaten",
            "infoEmpty": "Geen resultaten om weer te geven",
            "emptyTable": "Geen resultaten aanwezig in de tabel",
        },
        "order": [[0, "desc"]],
        paginate: false,
        lengthChange: false,
        filter: true,
        info:false,

        layout: {
            topEnd: {
                search: {
                    placeholder: '{{ __('messages.Zoeken') }}'
                }
            }
        }
    });


</script>

