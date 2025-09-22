
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
                    <p class="ms-1 text-sm font-medium text-gray-700 md:ms-2">Mijn orders</p>
                </div>
            </li>
        </ol>
    </nav>
</x-slot>


<div class="py-12">

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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
                    <div id="alert-2" class="flex items-center p-4 mb-4 text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
                        <svg class="shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                        </svg>
                        <span class="sr-only">Info</span>
                        <div class="ms-3 text-sm font-medium">
                            {{ session('error') }}
                        </div>
                        <button type="button" class="ms-auto -mx-1.5 -my-1.5 bg-red-50 text-red-500 rounded-lg focus:ring-2 focus:ring-red-400 p-1.5 hover:bg-red-200 inline-flex items-center justify-center h-8 w-8 dark:bg-gray-800 dark:text-red-400 dark:hover:bg-gray-700" data-dismiss-target="#alert-2" aria-label="Close">
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
                    <button type="button" class="text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-gray-700 dark:border-gray-700">
                        <a target="_blank" href="{{asset('/storage/uploads/rietpanel-order-formulier.pdf')}}"> <i class="fa-solid fa-download"></i> Download order formulier

                        </a>
                    </button>
                    <div class="relative md:absolute right-0 top-0">
                        @admin
                        <button wire:click="uploadOrderForm()" type="button" class="text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5  dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-gray-700 dark:border-gray-700">
                            <i class="fa-solid fa-upload"></i> Order formulier uploaden
                        </button>
                        @endadmin
                        <button wire:click="newOrder()" type="button" class="mt-[10px] text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5  dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-gray-700 dark:border-gray-700">
                            <i class="fa fa-plus hover:cursor-pointer"></i> Order aanmaken
                        </button>
                    </div>

                </div>
                <br/>
                <div class="grid">
                    <table id="pagination-table">
                        <thead>
                        <tr>
                            <th>
                                <span class="flex items-center">
                                    Order ID:
                                </span>
                            </th>
                            <th>
                                <span class="flex items-center">
                                    Project naam:
                                </span>
                            </th>
                            <th>
                                <span class="flex items-center">
                                    Geplaatst door:
                                </span>
                            </th>
                            <th>
                                <span class="flex items-center">
                                    Bedrijfsnaam:
                                </span>
                            </th>
                            <th>
                                <span class="flex items-center">
                                    Status:
                                </span>
                            </th>

                            <th>
                                 <span class="flex items-center">
                                    Totaal m²:
                                </span>
                            </th>

                            <th>
                                <span class="flex items-center">
                                    Download order:
                                </span>
                            </th>

                            @admin
                            <th>
                                <span class="flex items-center">
                                    Status bevestigen:
                                </span>
                            </th>
                            @endadmin
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($this->orders as $order)
                            <tr>
                                <td class="font-medium text-gray-900 whitespace-nowrap">
                                    {{$order->order_id}}
                                </td>
                                <td class="font-medium text-gray-900 whitespace-nowrap">
                                    {{$order->project_naam}}
                                </td>
                                <td class="font-medium text-gray-900 whitespace-nowrap">
                                    {{$order->intaker}}
                                </td>

                                <td class="font-medium text-gray-900 whitespace-nowrap">
                                    {{$order->user->bedrijfsnaam}}
                                </td>

                                <td class="font-medium @if($order->status == 'In behandeling') text-orange-500 @elseif($order->status == 'Bevestigd') text-green-500 @endif whitespace-nowrap">
                                    {{$order->status}}
                                </td>

                                <td class="font-medium text-center text-gray-900 whitespace-nowrap">
                                        <?php $totalM2 = 0?>
                                    @foreach($order->orderLines as $orderLine)
                                            <?php $totalM2 += $orderLine->m2;?>
                                    @endforeach
                                    {{$totalM2}} m²
                                </td>

                                <td class="font-medium text-center text-gray-900 whitespace-nowrap">
                                    <a href="{{asset('/storage/orders/order-'.$order->order_id.'.pdf')}}" target="_blank"><i class="fa-solid fa-download"></i></a>
                                </td>



                                @admin
                                <td class="font-medium text-center text-gray-900 whitespace-nowrap">
                                    <button wire:click="editOrder({{$order->id}})" @if($order->status == 'Bevestigd')disabled @endif class="disabled:cursor-not-allowed disabled:text-[#16a34a54] text-green-700">
                                        <i class="fa-solid fa-circle-check" ></i>
                                    </button>

                                </td>
                                @endadmin
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
    if (document.getElementById("pagination-table") && typeof simpleDatatables.DataTable !== 'undefined') {
        const dataTable = new simpleDatatables.DataTable("#pagination-table", {
            searchable: true,
            fixedHeight:true,

            labels: {
                placeholder: "Zoeken",
                info: "",
                noRows: 'Geen orders gevonden',
                noResults: "Geen orders gevonden",
            },
            sortable: false,
            perPageSelect: false
        });
    }
</script>
