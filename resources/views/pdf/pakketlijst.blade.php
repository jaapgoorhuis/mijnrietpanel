<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pakketlijst Order #{{ $order->order_id }}</title>

    <link rel="stylesheet" href="{{ asset('pdf.css') }}" type="text/css">

    <style>

        body{
            margin:0;
            padding:20px;
            font-family: Arial, Helvetica, sans-serif;
            font-size:12px;
        }

        table{
            width:100%;
            border-collapse:collapse;
        }

        td,th{
            border:1px solid #000;
            padding:4px;
            vertical-align:top;
        }

        th{
            background:#efefef;
            font-weight:bold;
        }

        .label{
            width:140px;
            font-weight:bold;
            text-align:right;
        }

        .logo{
            text-align:center;
            width:120px;
        }

        .page-break{
            page-break-before:always;
        }

        .pakket-table{
            margin-top:15px;
            margin-bottom:15px;
        }

        .subhead td{
            background:#000;
            color:#fff;
            font-weight:bold;
            text-align:center;
            border:none;
        }

        .waterstop{
            font-size:10px;
            line-height:1.25;
        }



        table{
            width:100%;
            border-collapse:collapse;
            border-spacing:0;
        }

        .waterstop div{
            margin-bottom:3px;
            border-bottom:1px solid #ddd;
            padding-bottom:2px;
        }

        .waterstop div:last-child{
            border:none;
            margin:0;
            padding:0;
        }

    </style>

</head>

<body>

@foreach($pakketten as $pakketIndex => $pakket)

    <div class="{{ $pakketIndex ? 'page-break' : '' }}">

        <table>

            <tr>

                <td class="label">
                    Ordernummer
                </td>

                <td>
                    {{ $order->order_id }}
                </td>

                <td rowspan=" @if($order->rietpanel_comment) 7 @else 6 @endif" class="logo">

                    <img src="{{ public_path('storage/images/rietpanel-R.png') }}"
                         style="width:90px;">

                </td>

            </tr>

            <tr>

                <td class="label">
                    Klantnaam
                </td>

                <td>
                    {{ $order->klantnaam }}
                </td>

            </tr>

            <tr>

                <td class="label">
                    Adres
                </td>

                <td>
                    {{ $order->aflever_straat }} {{$order->aflever_postcode}} {{ $order->aflever_plaats }}
                </td>

            </tr>

            <tr>

                <td class="label">
                    Projectnaam:
                </td>

                <td>
                    {{ $order->project_naam }}
                </td>

            </tr>

            <tr>

                <td class="label">
                    Project ref:
                </td>

                <td>
                    {{ $order->referentie }}
                </td>

            </tr>

            @if($order->rietpanel_comment)
            <tr>

                <td class="label">
                    Opmerkingen
                </td>

                <td>
                    {{ $order->rietpanel_comment }}
                </td>

            </tr>
            @endif

            <tr>

                <td class="label">
                    Pakket
                </td>

                <td>
                    {{ $pakketIndex+1 }}
                    /
                    {{ count($pakketten) }}
                </td>

            </tr>



        </table>

        @php

            $groepen=[];

            foreach($pakket as $paneel){

                $line=$paneel['orderLine'];

                $key=
                    $line->fillTotaleLengte.'_'.
                    $line->fillCb.'_'.
                    $line->lb;

                $groepen[$key][]=$line;

            }

        @endphp

        @foreach($groepen as $panelen)

            @php

                $line=$panelen[0];

            @endphp

            <table class="pakket-table">

                <tr class="subhead">
                    <td>Kleur</td>
                    <td>Toepassing</td>
                    <td>Merk</td>
                    <td>Kerndikte</td>
                    <td>m²</td>
                    <td>Aantal</td>



                </tr>

                <tr>

                    <td>
                        {{ $order->rietkleur }}
                    </td>

                    <td>
                        {{ $order->toepassing == 'Wand' ? 'Gevel' : 'Dak' }}
                    </td>
                    <td>
                        {{ $order->merk_paneel }}
                    </td>
                    <td>
                        {{ $order->kerndikte }}
                    </td>

                    <td>
                        {{ number_format($line->m2,2,',','.') }} m²
                    </td>

                    <td>
                        {{ count($panelen) }}
                    </td>

                </tr>

                <tr class="subhead">

                    <td>Lengte</td>
                    <td>LB</td>
                    <td>CB</td>
                    <td>Nokafschuining</td>
                    <td>Vrije ruimte</td>
                    <td>Waterstop</td>

                </tr>

                <tr>

                    <td>
                        {{ $line->fillTotaleLengte }} mm
                    </td>

                    <td>
                        {{ $line->lb ? $line->lb.' mm' : '-' }}
                    </td>

                    <td>
                        {{ $line->fillCb ? $line->fillCb.' mm' : '-' }}
                    </td>

                    <td>
                        {{ $line->nokafschuining ? $line->nokafschuining.'°' : '-' }}
                    </td>

                    <td>

                        @if($line->vrije_ruimte_2)

                            {{ $line->vrije_ruimte_2 }} mm

                            <br>

                            <small>
                                {{ $line->vrije_ruimte_1 }} mm vanaf boven
                            </small>

                        @else

                            -

                        @endif

                    </td>

                    <td class="waterstop">

                        @if($line->waterstops->count())

                            @foreach($line->waterstops as $waterstop)

                                <div>

                                    <strong>{{ $waterstop->type }} mm</strong><br>

                                    Vanaf bovenkant element: {{ $waterstop->vertical }} mm<br>

                                    @if($waterstop->horizontal < 0)

                                        Vanuit midden element: {{ abs($waterstop->horizontal) }} mm naar links

                                    @elseif($waterstop->horizontal > 0)

                                        Vanuit midden element: {{ $waterstop->horizontal }} mm naar rechts

                                    @else

                                        Gecentreerd in het midden van het element

                                    @endif

                                </div>

                            @endforeach

                        @elseif($line->waterstop_type)

                            <div>

                                <strong>{{ $line->waterstop_type }} mm</strong><br>

                                V: {{ $line->waterstop_vertical }} mm<br>

                                @if($line->waterstop_horizontal < 0)

                                    H: {{ abs($line->waterstop_horizontal) }} mm links

                                @elseif($line->waterstop_horizontal > 0)

                                    H: {{ $line->waterstop_horizontal }} mm rechts

                                @else

                                    Midden

                                @endif

                            </div>

                        @else

                            -

                        @endif

                    </td>

                </tr>


            </table>

        @endforeach

        <table style="margin-top:20px;">

            <tr>

                <td style="border:none;text-align:left;">

                    <img src="{{ public_path('storage/images/rietpanel_logo.png') }}"
                         style="width:140px;">

                </td>

                <td style="border:none;text-align:right;">

                    <strong>

                        Pakket

                        {{ $pakketIndex+1 }}

                        van

                        {{ count($pakketten) }}

                    </strong>

                </td>

            </tr>

        </table>

    </div>

@endforeach

</body>
</html>
