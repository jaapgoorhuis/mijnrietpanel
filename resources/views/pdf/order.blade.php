<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Order</title>
    <link rel="stylesheet" href="{{ asset('pdf.css') }}" type="text/css">
</head>
<body>
<div class="page-container" style="padding:15px;">
    <table class="w-full">
        <tr>
            <td class="w-half">
                <img src="{{ public_path("storage/images/rietpanel_logo.png")}}" alt="" style="width: 200px;"/>
            </td>
        </tr>
    </table>

    <?php
    $company = \App\Models\Company::where('id', $order->user->bedrijf_id)->first();

    /*
    |--------------------------------------------------------------------------
    | Opgeslagen prijsgegevens
    |--------------------------------------------------------------------------
    | De PDF rekent geen prijzen/toeslagen meer opnieuw uit.
    | Alles wordt opgeslagen via PricingServices::updateDocumentPricing()
    | tijdens aanmaken/bewerken van de order.
    */
    $orderLines = $orderLines ?? $order->orderLines;

    $toeslagen = $order->surcharges ?? collect();
    $hasToeslagen = $toeslagen->count() > 0;

    $totalPrice = (float) ($order->subtotal ?? 0);
    $totalToeslagPrice = (float) ($order->surcharges_total ?? 0);
    $totalBtw = (float) ($order->vat_total ?? 0);
    $grandTotal = (float) ($order->grand_total ?? 0);
    $totalM2 = $orderLines->sum('m2');
    ?>

    <div class="margin-top">
        <table class="w-full">
            <tr>
                <td class="w-half">
                    <div>Order #: {{$order->order_id}}</div>
                    <div>{{ __('messages.Order datum') }}: {{date("d-m-Y", strtotime($order->created_at))}}</div><br/>
                    <div>{{ __('messages.Bedrijfs-id') }}: {{$company->id}}</div>

                    <div>{{ __('messages.Referentie') }}: {{$order->referentie}}</div>
                    <div>{{ __('messages.Verkoper') }}: {{$order->intaker}}</div>
                    @if($order->status == 'In behandeling')
                        <div>{{ __('messages.Gewenste afleverdatum') }}: {{$order->requested_delivery_date}}</div>
                    @else
                        <div>{{ __('messages.Afleverdatum') }}: {{$order->delivery_date}}</div>
                    @endif
                </td>
                <td class="w-half">
                    <div>{{$company->straat}}</div>
                    <div>{{$company->postcode}}</div>
                    <div>{{$company->plaats}}</div>
                    <div>M: {{$order->user->email}}</div>
                    <div>T: {{$order->user->phone}}</div>
                </td>
            </tr>
        </table>

        <br/>

        <table>
            <tr>
                <td class="w-half">
                    <div><strong>Aan:</strong></div>
                    <div>{{ __('messages.Klant naam') }}: {{$order->klantnaam}}</div>
                    <div>{{ __('messages.Adres') }}: {{$order->aflever_straat}}</div>
                    <div>{{ __('messages.Postcode') }}: {{$order->aflever_postcode}}</div>
                    <div>{{ __('messages.Plaats') }}: {{$order->aflever_plaats}}</div>
                    <div>{{ __('messages.Land') }}: {{$order->aflever_land}}</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="margin-top">
        <h3>Order</h3>
        <table class="products">
            <tr>
                <th>{{ __('messages.Rietkleur') }}</th>
                <th>{{ __('messages.Toepassing') }}</th>
                <th>{{ __('messages.Merk') }}</th>
                <th>{{ __('messages.Kerndikte') }}</th>
            </tr>
            <tr class="items">
                <td>{{$order->rietkleur}}</td>
                <td>{{$order->toepassing}}</td>
                <td>{{$order->merk_paneel}}</td>
                <td>{{$order->kerndikte}}</td>
            </tr>
        </table>

        <h3>Panelen</h3>
        <table class="products">
            <thead>
            <tr>
                <th>#</th>
                <th>{{ __('messages.Lengte') }}</th>
                @if($showCb)
                    <th>{{ __('messages.CB') }}</th>
                @endif
                @if($showLb)
                    <th>{{ __('messages.LB') }}</th>
                @endif
                @if($showNokafschuining)
                    <th>{{ __('messages.Nokafschuining') }}</th>
                @endif
                @if($showVrijeRuimte)
                    <th>{{ __('messages.Vrije ruimte') }}</th>
                @endif
                <th>m²</th>
                <th>{{ __('messages.Aantal') }}</th>
                <th>{{ __('messages.Prijs') }}</th>
            </tr>
            </thead>
            <tbody>
            <?php $count = 0; ?>

            @foreach($orderLines as $orderLine)
                    <?php $count++; ?>
                <tr class="items">
                    <td>{{ $count }}</td>
                    <td>{{ $orderLine->fillTotaleLengte }} mm</td>
                    @if($showCb)
                        <td>{{ $orderLine->fillCb > 0 ? $orderLine->fillCb . ' mm' : '' }}</td>
                    @endif
                    @if($showLb)
                        <td>{{ $orderLine->lb > 0 ? $orderLine->lb . ' mm' : '' }}</td>
                    @endif
                    @if($showNokafschuining)
                        <td>{!! $orderLine->nokafschuining > 0 ? $orderLine->nokafschuining . ' &deg;' : '' !!}</td>
                    @endif

                    @if($showVrijeRuimte)
                        <td>
                            {{ $orderLine->vrije_ruimte_2 > 0
                                ? $orderLine->vrije_ruimte_2 . ' mm (' . $orderLine->vrije_ruimte_1 . ' ' . __('messages.mm vanaf boven') . ')'
                                : ''
                            }}
                        </td>
                    @endif

                    <td>{{ $orderLine->m2 }} m²</td>
                    <td>{{ $orderLine->aantal }}</td>
                    <td>
                            <?php
                            $lineTotal = (float) $orderLine->m2 * (float) ($orderLine->price_per_m2 ?? 0);
                            ?>
                        {!! '&euro;&nbsp;' . number_format($lineTotal, 2, ',', '.') !!}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        @if($hasToeslagen)
            <table class="products toeslagen">
                <tr class="items">
                    <td><strong>{{ __('messages.Toeslag') }}</strong></td>
                    <td><strong>{{ __('messages.Stuks') }}</strong></td>
                    <td><strong>{{ __('messages.Stuksprijs') }}</strong></td>
                    <td><strong>{{ __('messages.Totaal') }}</strong></td>
                </tr>

                @foreach($toeslagen as $toeslag)
                    <tr class="items">
                        <td>{{ __('messages.' . $toeslag->name) }}</td>
                        <td>{{ $toeslag->qty }}</td>
                        <td>{!! '&euro;&nbsp;' . number_format($toeslag->unit_price, 2, ',', '.') !!}</td>
                        <td>{!! '&euro;&nbsp;' . number_format($toeslag->total, 2, ',', '.') !!}</td>
                    </tr>
                @endforeach
            </table>
        @endif

        @if($order->comment)
            <table class="products toeslagen">
                <tr class="items">
                    <td><strong>{{ __('messages.Klant opmerking') }}</strong></td>
                    @if($order->orderRules)
                        <td>
                            <strong>{{__('messages.Invloed op prijs')}}</strong>
                        </td>
                    @endif
                </tr>
                <tr class="items">
                    <td>{{$order->comment}}</td>
                    @if($order?->status === 'Bevestigd' && $order?->orderRules)
                        <td>
                            &euro;&nbsp;{{ number_format($order->orderRules->price, 2, ',', '.') }}
                        </td>
                    @endif
                </tr>
            </table>
        @endif
    </div>

    <div class="total" style="width: 100%; margin-left:auto; margin-top:50px;">
        <table class="total-table">

            <!-- Totaal m2 -->
            <tr>
                <th style="text-align: left;">
                    {{ __('messages.Totaal') }} m²:
                </th>

                <th style="text-align: left;">
                    {{ number_format($totalM2, 2, ',', '.') }} m²
                </th>
            </tr>

            <!-- Subtotaal -->
            <tr>
                <th style="text-align: left;">
                    {{ __('messages.Subtotaal') }}:
                </th>

                <th style="text-align: left;">
                    {!! '&euro;&nbsp;' . number_format($totalPrice, 2, ',', '.') !!}
                </th>
            </tr>

            <!-- Toeslagen -->
            @if($hasToeslagen)
                <tr>
                    <th style="text-align: left;">
                        {{ __('messages.Toeslagen') }}:
                    </th>

                    <th style="text-align: left;">
                        {!! '&euro;&nbsp;' . number_format($totalToeslagPrice, 2, ',', '.') !!}
                    </th>
                </tr>
            @endif

            <!-- BTW totaal -->
            <tr>
                <th style="text-align: left;">
                    21% BTW:
                </th>

                <th style="text-align: left;">
                    {!! '&euro;&nbsp;' . number_format($totalBtw, 2, ',', '.') !!}
                </th>
            </tr>

            <!-- Extra order rule -->
            @if($order->orderRules)
                <tr>
                    <th style="text-align: left;">
                        {{$order->orderRules->rule}}:
                    </th>

                    <th style="text-align: left;">
                        {!! '&euro;&nbsp;' . number_format($order->orderRules->price, 2, ',', '.') !!}
                    </th>
                </tr>
            @endif

            <!-- Eindtotaal -->
            <tr>
                <th style="text-align: left; border-top: 1px solid #000;">
                    <strong>
                        {{ __('messages.Totaal') }} incl. 21% BTW

                        @if($hasToeslagen)
                            , incl. {{ __('messages.toeslagen') }}
                        @endif
                    </strong>
                </th>

                <th style="text-align: left; border-top: 1px solid #000;">
                    <strong>
                        {!! '&euro;&nbsp;' . number_format($grandTotal, 2, ',', '.') !!}
                    </strong>
                </th>
            </tr>

        </table>
    </div>

    <!-- Footer altijd onderaan pagina -->
    <div class="footer" style="position: fixed; padding:15px; bottom: 0; left: 0; width: 100%; font-size: 0.75rem; line-height: 1.4; border-top: 1px solid #000; padding-top:5px;">
        <p><strong>{{ __('messages.Betalingsconditie') }}:</strong>{{ __('messages.14 dagen netto') }}</p>
        <p>
            {!!  __('messages.orderConditions') !!}

        </p>
    </div>
</div>

<style>
    body, html {
        margin: 0;
        padding: 0;
        font-family: Arial, sans-serif;
        font-size: 0.8rem;
    }

    .w-full { width: 100%; }
    .w-half { width: 50%; }
    .margin-top { margin-top: 1.25rem; }

    /* Producten tabel */
    table.products {
        width: 100%;
        font-size: 0.8rem;
        border-collapse: collapse;
        margin-top: 20px;
    }
    table.products th {
        background-color: #000;
        color: #fff;
        padding: 0.5rem;
        text-align: left;
    }
    table.products tr.items {
        background-color: #f9fafb;
    }
    table.products td {
        padding: 0.5rem 0.5rem 0.5rem 1rem;
    }

    /* Toeslagen tabel */
    table.toeslagen {
        width: 100%;
        margin-top: 25px;
        border-collapse: collapse;
    }
    table.toeslagen th {
        background-color: #000;
        color: #fff;
        padding: 0.5rem;
        text-align: left;
    }
    table.toeslagen tr.items {
        background-color: #f9fafb;
    }
    table.toeslagen td {
        padding: 0.5rem 0.5rem 0.5rem 1rem;
    }

    /* Totale prijzen tabel */
    .total-table {
        width: 50%; /* prijsblok breedte */
        border-collapse: separate;
        border-spacing: 0 2px;
        font-weight: normal;
        margin-left: auto; /* rechts uitlijnen */
        margin-right: 0;
    }
    .total-table th {
        padding: 2px 2px;
        font-weight: normal;
    }
    .total-table tr:last-child th {
        padding-top: 2px;
        font-weight: bold;
    }

    /* Separator lijn */
    .price-separator {
        border: none;
        border-top: 1px solid #000;
        margin: 10px 0;
        width: 100%;
    }

    /* Betalingsvoorwaarden */
    .payment-conditions {
        text-align: left;
        font-size: 0.75rem;
        line-height: 1.4;
        color: #000;
        margin-top: 10px;
    }
    .payment-conditions p {
        margin: 4px 0;
    }
    .payment-conditions a {
        color: #000;
        text-decoration: none;
    }

    /* Footer container */
    .total-footer-container {
        margin-top: 50px; /* ruimte van content erboven */
        page-break-inside: avoid; /* voorkomt breuk in PDF */
    }

    /* Afbeeldingen */
    img {
        max-width: 100%;
        height: auto;
    }
</style>
</body>
</html>
