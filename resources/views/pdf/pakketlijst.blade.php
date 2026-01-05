<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pakketlijst Order #{{ $order->order_id }}</title>
    <link rel="stylesheet" href="{{ asset('pdf.css') }}" type="text/css">
    <style>
        body {
            margin: 0;
            padding: 20px;
            text-align: center;
        }

        table {
            text-align:left;
            margin: 0 auto; /* Geen extra marge tussen tabellen */
            border-collapse: collapse;
            width: 100%;
        }

        td {
            border: 1px solid black;
            padding: 3px; /* Iets strakker dan 5px voor compactheid */
            vertical-align: top;
        }

        .label {
            text-align:right;
            font-weight: bold;
            width: 150px;
        }

        .logo {
            text-align: center;
            width: 100px;
        }

        .page-break {
            page-break-before: always;
        }

        tr.head> td {
            font-weight:bold;
            background: #eee;
            text-align: center;
        }

        /* Verwijder extra ruimte tussen tabellen */
        table + table {
            margin-top: 0;
        }
    </style>
</head>
<body>

@foreach ($pakketten as $index => $pakket)
    <div class="{{ $index > 0 ? 'page-break' : '' }}">
        <!-- Ordergegevens bovenaan elke pagina -->
        <table class="text">
            <tr>
                <td class="label">Ordernummer:</td>
                <td>{{ $order->order_id }}</td>
                <td rowspan="4" class="logo">
                    <img src="{{ public_path('storage/images/rietpanel-R.png') }}" alt="" style="width: 100px;">
                </td>
            </tr>
            <tr>
                <td class="label">Klantnaam:</td>
                <td>{{ $order->klantnaam }}</td>
            </tr>
            <tr>
                <td class="label">Plaats:</td>
                <td>{{ $order->aflever_plaats }}</td>
            </tr>
            <tr>
                <td class="label">Project / ref:</td>
                <td>{{ $order->referentie }}</td>
            </tr>

        </table>

        <!-- Pakket tabel -->
        <table>
            <thead>
            <tr class="head">
                <td>Aantal panelen</td>
                <td>Lengte mm</td>
                <td>Kerndikte mm</td>
                <td>CB mm</td>
            </tr>
            </thead>
            <tbody>
            @php
                // Groepeer panelen per lengte en CB
                $groepen = [];
                foreach ($pakket as $paneel) {
                    $groepen[$paneel['lengte']][$paneel['cb']][] = $paneel;
                }
            @endphp
            @foreach ($groepen as $lengte => $cbs)
                @foreach ($cbs as $cbValue => $panelen)
                    <tr>
                        <td>{{ count($panelen) }}</td>
                        <td>{{ $lengte }} mm</td>
                        <td>{{ $order->kerndikte }}</td>
                        <td>{{ $cbValue }} mm</td>
                    </tr>
                @endforeach
            @endforeach
            </tbody>
        </table>

        <!-- Logo onderaan per pagina -->
        <table>
            <tr>
                <td>
                    <img src="{{ public_path('storage/images/rietpanel_logo.png') }}" alt="" style="width: 100px; padding:0; margin:0;">
                </td>
            </tr>
        </table>
    </div>
@endforeach

</body>
</html>
