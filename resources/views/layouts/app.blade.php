<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>
        <link rel="icon" type="image/x-icon" href="{{asset('/storage/images/favicon.png')}}">
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <script src="https://kit.fontawesome.com/a865bbd52d.js" crossorigin="anonymous"></script>
        <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />


        <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
        <script
            src="https://code.jquery.com/jquery-3.7.1.min.js"
            integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo="
            crossorigin="anonymous"></script>

        <link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />
        <!-- Scripts -->
        <script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/2.3.4/js/dataTables.tailwindcss.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@floating-ui/core@1.7.2"></script>
        <script src="https://cdn.jsdelivr.net/npm/@floating-ui/dom@1.7.2"></script>
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @livewireStyles
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
                <div class="fixed bottom-0 right-0 p-2">
                    <a href="https://crewa.nl"><img src="{{asset('storage/images/crewa-logo.png')}}" class="h-[20px]"/></a>
                </div>
            </main>
        </div>
        <livewire:scripts />
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            window.addEventListener('popstate', function () {
                location.reload();
            });
        });
    </script>
        <script>
            $(document).ready(function() {
                $('.custom-datatable').each(function () {
                    if ($.fn.DataTable.isDataTable(this)) {
                        $(this).DataTable().clear().destroy();
                    }

                    $(this).DataTable({
                        language: {
                            "info": "_START_ tot _END_ van _TOTAL_ resultaten",
                            "infoEmpty": "Geen resultaten om weer te geven",
                            "emptyTable": "Geen resultaten aanwezig in de tabel",
                            searchPlaceholder: 'Zoeken...',
                        },
                        paging: false,
                        lengthChange: false,
                        searching: true,
                        info: false,
                        responsive: true,
                    });
                });

                $('.dt-container .grid').each(function (index) {
                    if ($(this).hasClass('grid-cols-2')) {
                        $(this).removeClass('grid-cols-2').addClass('grid-cols-1 md:grid-cols-2');
                    }
                });

                $('.dt-search label').contents().filter(function () {
                    return this.nodeType === 3; // Node type 3 = text
                }).remove();


                $('.dt-container').each(function () {


                    const $grids = $(this).find('.grid');

                    // Check of er minstens 2 grids zijn
                    if ($grids.length >= 2) {
                        const $secondGrid = $grids.eq(1);

                        // Alleen op kleine schermen

                        $secondGrid.addClass('overflow-x-auto');

                    }
                });

                $('input[type="search"]').each(function () {
                    // Haal alle class-namen op
                    const originalClasses = $(this).attr('class') || '';

                    // Filter alle klassen die niet met "dark:" beginnen
                    const cleanedClasses = originalClasses
                        .split(' ')
                        .filter(c => !c.startsWith('dark:'))
                        .join(' ');

                    // Zet de gefilterde klassen terug
                    $(this).attr('class', cleanedClasses);
                });

                $('input[type="search"]').on('focus', function () {
                    $(this).css({
                        'border-color': 'rgba(192, 161, 110, 0.5)',
                        'box-shadow': '0 0 0 3px rgba(192, 161, 110, 0.5)'
                    });
                });

                $('input[type="search"]').on('blur', function () {
                    // Reset eventueel naar standaard of verwijder inline styles
                    $(this).css({
                        'border-color': '',
                        'box-shadow': ''
                    });
                });
            });



        </script>


    </body>


</html>
