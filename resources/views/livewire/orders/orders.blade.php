
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
                    <p class="ms-1 text-sm font-medium text-gray-700 md:ms-2">@admin {{ __('messages.Alle orders') }}  @endadmin @user {{ __('messages.Mijn orders') }} @enduser</p>
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

                <div class="relative mb-[20px]">

                    <div class="relative md:absolute right-0 top-0 ">

                        <button wire:click="newOrder()" type="button" class="w-full sm:w-auto mt-[10px] text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5">
                            <i class="fa fa-plus hover:cursor-pointer"></i> {{ __('messages.Order aanmaken') }}
                        </button>
                    </div>
                </div>
                <br/>

                    <table id="pagination-table" class=" w-full text-sm text-left text-gray-500 ">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th scope="col" class="px-4 py-3">ORDER ID</th>
                            <th scope="col" class="px-4 py-3">Project</th>
                            <th scope="col" class="px-4 py-3"> {{ __('messages.Geplaatst door') }}</th>
                            <th scope="col" class="px-4 py-3"> {{ __('messages.Bedrijfsnaam') }}</th>
                            <th scope="col" class="px-4 py-3"> {{ __('messages.Gewenste leverdatum') }}</th>
                            <th scope="col" class="px-4 py-3"> {{ __('messages.Leverdatum rietpanel') }}</th>
                            <th scope="col" class="px-4 py-3">Status</th>
                            @admin
                            <th scope="col" class="px-4 py-3"> {{ __('messages.land') }}</th>
                            @endadmin

                            @admin
                                <th scope="col" class="px-4 py-3"> {{ __('messages.Order besteld') }}</th>
                            @endadmin
                            <th scope="col" class="px-4 py-3"> {{ __('messages.Aantal') }} m²</th>
                            <th scope="col" class="px-4 py-3 text-right">
                                <span> {{ __('messages.acties') }}</span>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($this->orders as $order)
                            <tr class="border-b ">
                                <th scope="row" class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap">{{$order->order_id}}</th>
                                <td class="px-4 py-3">{{$order->project_naam}}</td>
                                <td class="px-4 py-3">{{$order->intaker}}</td>
                                <td class="px-4 py-3">@if($order->user)
                                        {{$order->user->company->bedrijfsnaam}}
                                    @else
                                Gebruiker is verwijderd
                                    @endif
                                </td>
                                <td class="px-4 py-3">@if($order->requested_delivery_date) {{$order->requested_delivery_date}} @else  {{ __('messages.Geen datum') }} @endif</td>
                                <td class="px-4 py-3">@if($order->delivery_date) {{$order->delivery_date}} @else {{ __('messages.Geen datum') }} @endif</td>
                                <td class="px-4 py-3 @if($order->status == 'In behandeling') text-orange-500 @elseif($order->status == 'Bevestigd') text-green-500  @endif whitespace-nowrap">
                                    {{ __('messages.'.$order->status) }}
                                </td>

                                @admin
                                <td class="px-4 py-3">
                                    @if($order->lang === 'nl')
                                        NL
                                    @else
                                        {{ __('messages.Buitenland')}}
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if($order->order_ordered == '')
                                        {{ __('messages.Nee')}}
                                    @else
                                        {{$order->order_ordered}}
                                    @endif
                                </td>
                                @endadmin
                                <td class="px-4 py-3">
                                        <?php $totalM2 = 0?>
                                    @foreach($order->orderLines as $orderLine)
                                            <?php $totalM2 += $orderLine->m2;?>
                                    @endforeach
                                    {{$totalM2}} m²
                                </td>

                                <td  class="px-4 py-3 flex items-center justify-end">
                                    <button wire:ignore.self id="{{$order->id}}-dropdown-button" data-dropdown-toggle="{{$order->id}}-dropdown" class="inline-flex items-center p-0.5 text-sm font-medium text-center text-gray-500 hover:text-gray-800 rounded-lg focus:outline-none " type="button">
                                        <svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z" />
                                        </svg>
                                    </button>
                                    <div wire:ignore.self id="{{$order->id}}-dropdown" class="hidden z-10 w-auto bg-white rounded divide-y divide-gray-100 shadow block " style="position: absolute; inset: 0px auto auto 0px; margin: 0px; transform: translate(1412px, 425px);" data-popper-placement="bottom">
                                        <ul class="py-1 text-sm text-gray-700 " aria-labelledby="{{$order->id}}-dropdown-button">
                                            @admin
                                            <li>
                                                <button class="block py-2  px-4 text-left w-full hover:bg-gray-100 disabled:cursor-not-allowed disabled:text-[#16a34a54]" wire:click="confirmOrder({{$order->id}})" @if($order->status == 'Bevestigd')disabled @endif>
                                                    <i class="fa-solid fa-circle-check" ></i> Order bevestigen
                                                </button>
                                            </li>
                                            @endadmin
                                            <li>
                                                <a class="block py-2 px-4 hover:bg-gray-100" href="{{asset('/download-order/order-'.$order->order_id)}}" target="_blank">
                                                    <i class="fa-solid fa-download"></i>{{ __('messages.Order downloaden') }}
                                                </a>
                                            </li>
                                            @admin
                                            <li>
                                                <a
                                                    @if($order->status == 'In behandeling')
                                                        style="background-color:#e9eaeb; color:#b5aeae; cursor: not-allowed;"
                                                    href="javascript:void(0)"
                                                    @else
                                                        href="{{ asset('/download-pakketlijst/pakketlijst-'.$order->order_id) }}"
                                                    target="_blank"
                                                    @endif
                                                    class="block py-2 px-4 hover:bg-gray-100"
                                                >
                                                    <i class="fa-solid fa-download"></i> Pakketlijst downloaden
                                                </a>
                                            </li>
                                            <li>
                                                <a
                                                    @if($order->status == 'In behandeling')
                                                        style="background-color:#e9eaeb; color:#b5aeae; cursor: not-allowed;"
                                                    href="javascript:void(0)"
                                                    @else
                                                        href="{{ asset('/download-zaaglijst/fabrieklijst-'.$order->order_id) }}"
                                                    target="_blank"
                                                    @endif
                                                    class="block py-2 px-4 hover:bg-gray-100"
                                                >
                                                    <i class="fa-solid fa-download"></i> Fabrieklijst downloaden
                                                </a>


                                            </li>
                                            <li>
                                                <button @if($order->status == 'In behandeling') disabled @endif @if($order->order_ordered) disabled @endif class="block py-2  px-4 text-left w-full hover:bg-gray-100 disabled:cursor-not-allowed disabled:bg-[#e9eaeb] disabled:text-[#b5aeae]" wire:click="SendOrderList({{$order->id}})">
                                                    <i class="fa-solid fa-download"></i> Bestellijst versturen
                                                </button>

                                            </li>

                                            <li>
                                                <a
                                                    @if($order->status == 'In behandeling')
                                                        style="background-color:#e9eaeb; color:#b5aeae; cursor: not-allowed;"
                                                    href="javascript:void(0)"
                                                    @else
                                                        href="{{ asset('/download-orderlist/bestellijst-'.$order->order_id) }}"
                                                    target="_blank"
                                                    @endif
                                                    class="block py-2 px-4 hover:bg-gray-100"
                                                >
                                                    <i class="fa-solid fa-download"></i> Bestellijst downloaden
                                                </a>
                                            </li>
                                            @endadmin
                                            @admin
                                                <li>
                                                    <button @if($order->status == "Bevestigd") disabled @endif class="disabled:cursor-not-allowed disabled:text-[#00000038] block py-2  px-4 text-left w-full hover:bg-gray-100" wire:click="changeOrder({{$order->id}})">
                                                        <i class="fas fa-edit"></i>{{ __('messages.Order bewerken') }}
                                                    </button>
                                                </li>
                                                <li>
                                                    <button class="block py-2  px-4 text-left w-full hover:bg-gray-100" wire:click="removeOrder({{$order->id}})">
                                                        <i class="fa-solid fa-circle-check" ></i> {{ __('messages.Order verwijderen') }}
                                                    </button>
                                                </li>
                                            @endadmin
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
<script>
     document.addEventListener('open-new-tab', function (event) {
         window.open(event.detail[0].url, '_blank');
     });

     new DataTable("#pagination-table", {

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

