
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
                    <p class="ms-1 text-sm font-medium text-gray-700 md:ms-2">Statistieken</p>
                </div>
            </li>
        </ol>
    </nav>
</x-slot>


<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(Session::has('success'))
            <div id="alert-3" class="flex items-center p-4 mb-4 text-green-800 rounded-lg bg-green-50 " role="alert">
                <svg class="shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                </svg>
                <span class="sr-only">Info</span>
                <div class="ms-3 text-sm font-medium">
                    {{ session('success') }}
                </div>
                <button type="button" class="ms-auto -mx-1.5 -my-1.5 bg-green-50 text-green-500 rounded-lg focus:ring-2 focus:ring-green-400 p-1.5 hover:bg-green-200 inline-flex items-center justify-center h-8 w-8 " data-dismiss-target="#alert-3" aria-label="Close">
                    <span class="sr-only">Close</span>
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                </button>
            </div>
        @endif
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h2 class="text-lg font-medium text-gray-900">
                    Bedrijfsstatistieken
                </h2>
                <div class="overflow-x-auto">
                    <table id="statistics-table" class="custom-datatable w-full text-sm text-left text-gray-500  mt-[25px]">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 ">
                        <tr>
                            <th scope="col" class="px-4 py-3">Bedrijfsnaam:</th>
                            <th scope="col" class="px-4 py-3">Afgenomen m² (dit jaar):</th>
                            <th scope="col" class="px-4 py-3">Orders (dit jaar):</th>
                            <th scope="col" class="px-4 py-3">Offertes (dit jaar):</th>
                            <th scope="col" class="px-4 py-3 text-right">
                                <span>Actie's</span>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        @if($this->companys)
                            @foreach($this->companys as $company)
                                <tr class="border-b ">
                                    <th scope="row" class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap">{{$company->bedrijfsnaam}}</th>
                                    <th scope="row" class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap">
                                        @php
                                            $count = $company->users
                                                ->flatMap(fn($user) => $user->ordersYear)
                                                ->flatMap(fn($order) => $order->orderLines)
                                                ->sum('m2');
                                        @endphp

                                        {{ round($count,2) }} m²
                                    </th>

                                    <th scope="row" class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap">
                                        @php
                                            $count = $company->users
                                                ->flatMap->ordersYear
                                                ->count();
                                        @endphp
                                        {{ round($count,2) }} orders

                                    </th>

                                    <th scope="row" class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap">
                                        @php
                                            $count = $company->users
                                                ->flatMap->offertesYear
                                                ->count();
                                        @endphp
                                        {{ round($count,2) }} offertes

                                    </th>


                                    <td  class="px-4 py-3 flex items-center justify-end">
                                        <button wire:ignore.self id="{{$company->id}}-dropdown-button" data-dropdown-toggle="{{$company->id}}-dropdown" class="inline-flex items-center p-0.5 text-sm font-medium text-center text-gray-500 hover:text-gray-800 rounded-lg focus:outline-none " type="button">
                                            <svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z" />
                                            </svg>
                                        </button>
                                        <div wire:ignore.self id="{{$company->id}}-dropdown" class="hidden z-10 w-auto bg-white rounded divide-y divide-gray-100 shadow block " style="position: absolute; inset: 0px auto auto 0px; margin: 0px; transform: translate(1412px, 425px);" data-popper-placement="bottom">
                                            <ul class="py-1 text-sm text-gray-700 " aria-labelledby="{{$company->id}}-dropdown-button">
                                                <li>
                                                    <button
                                                        class="block py-2  px-4 text-left w-full hover:bg-gray-100 disabled:cursor-not-allowed disabled:text-[#16a34a54]"
                                                        wire:click="expandedCompanyStats({{$company->id}})">
                                                        <i class="fa-solid fa-pen-to-square"></i> Bekijk uitgebreide statistieken
                                                    </button>
                                                </li>


                                            </ul>

                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
