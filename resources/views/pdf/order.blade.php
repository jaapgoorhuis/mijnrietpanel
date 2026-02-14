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
    ?>

    <div class="margin-top">
        <table class="w-full">
            <tr>
                <td class="w-half">
                    <div>Order #: {{$order->order_id}}</div>
                    <div>{{ __('messages.Order datum') }}: {{date("d-m-Y", strtotime($order->created_at))}}</div><br/>
                    <div>{{ __('messages.Klantnummer') }}: {{$order->user->id}}</div>

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
        <table class="products">
            <tr>
                <th>{{ __('messages.Rietkleur') }}</th>
                <th>{{ __('messages.Toepassing') }}</th>
                <th>{{ __('messages.Merk') }}</th>
                <th>{{ __('messages.Kerndikte') }}</th>
                <th>{{ __('messages.Lengte') }}</th>
                <th>{{ __('messages.CB') }}</th>
                <th>m²</th>
                <th>{{ __('messages.Aantal') }}</th>
                <th>{{ __('messages.Prijs') }}</th>
            </tr>

            <?php $totalPrice= 0 ?>
            <!--            zaaglengtes zijn de panelen die onder de 2500mm zijn-->
            <?php $zaaglengtes = 0 ?>
            <?php $count = 0; ?>
            <?php $zaaglengteToeslag = \App\Models\Surcharges::where('rule', 'zaaglengte')->first(); ?>


            @foreach($orderLines as $key =>  $orderLine)
                {{$count++}}
                <tr class="items">
                    <td>{{$order->rietkleur}}</td>
                    <td>{{$order->toepassing}}</td>
                    <td>{{$order->merk_paneel}}</td>
                    <td>{{$order->kerndikte}}</td>
                    <td>{{$orderLine->fillTotaleLengte}} mm</td>
                    <td>{{$orderLine->fillCb}} mm</td>
                    <td>{{$orderLine->m2}} m²</td>
                    <td>{{$orderLine->aantal}}</td>
                    <td>
                            <?php
                            $panelType = \App\Models\PanelType::where('name', $order->kerndikte)->first();
                            if($company->is_reseller) {
                                $priceRule = \App\Models\PriceRules::where('panel_type', $panelType->id)->where('company_id', $company->id)->first();
                                $discount = 0;
                            } else {
                                $priceRule = \App\Models\PriceRules::where('panel_type', $panelType->id)->first();
                                $discount = $priceRule->price/100*$company->discount;
                            }

                            $m2priceBeforeDiscount = $priceRule->price - $discount;
                            $orderDiscount = $m2priceBeforeDiscount/100*$order->discount;
                            $orderMarge = $m2priceBeforeDiscount/100*$order->marge;

                            if($company->is_reseller) {
                                $m2price = $m2priceBeforeDiscount - $orderDiscount;
                            } else if($order->marge != 0) {
                                $m2price = $m2priceBeforeDiscount + $orderMarge;
                            } else {
                                $m2price = $m2priceBeforeDiscount;
                            }

                            $totalPrice += $orderLine->m2 * $m2price;

                            if($zaaglengteToeslag) {
                                if($orderLine->fillTotaleLengte < $zaaglengteToeslag->number) {
                                    $zaaglengtes += $orderLine->aantal;
                                }
                            }
                            ?>

                        {!! '&euro;&nbsp;' . number_format($orderLine->m2 * $m2price, 2, ',', '.') !!}
                    </td>
                </tr>
            @endforeach
        </table>

        <?php $btw = $totalPrice /100 *21 ?>
        <?php $toeslagen = \App\Models\Surcharges::get(); ?>
        <?php $vierkantemeterToeslag = \App\Models\Surcharges::where('rule', 'vierkantemeter')->first(); ?>
        <?php $totalToeslagPrice = 0?>
        <?php $allInPrice = $totalPrice + $btw ?>
        <?php $totalM2 = 0 ?>
        @foreach($order->orderLines as $orderLine)
                <?php $totalM2 += $orderLine->m2; ?>
        @endforeach


        @if($zaaglengtes > 0 || $totalM2 < $vierkantemeterToeslag->number )
            <table class="products toeslagen">
                <tr class="items">
                    <td><strong>{{ __('messages.Toeslag') }}</strong></td>
                    <td><strong>{{ __('messages.Stuks') }}</strong></td>
                    <td><strong>{{ __('messages.Stuksprijs') }}</strong></td>
                    <td><strong>{{ __('messages.Totaal') }}</strong></td>
                </tr>

                @foreach($toeslagen as $toeslag)
                    @if($toeslag)
                        @if($toeslag->rule == 'vierkantemeter')
                            <tr class="items">
                                @if($totalM2 < $toeslag->number)
                                    <td>{{ __('messages.'.$toeslag->name) }}</td>
                                    <td>1</td>
                                    <td>{!! '&euro;&nbsp;' . number_format($toeslag->price, 2, ',', '.') !!}</td>
                                    <td>{!! '&euro;&nbsp;' . number_format($toeslag->price, 2, ',', '.') !!}</td>
                                    <?php $totalToeslagPrice += $toeslag->price; ?>
                                @endif

                            </tr>
                        @endif

                        @if($toeslag->rule == 'zaaglengte')
                            @if($zaaglengtes > 0)
                            <tr class="items">
                                    <?php $zaagprijs = $zaaglengtes * $toeslag->price ?>
                                <td>{{ __('messages.'.$toeslag->name) }}</td>
                                <td>{{$zaaglengtes}}</td>
                                <td>{!! '&euro;&nbsp;' . number_format($toeslag->price, 2, ',', '.') !!}</td>
                                <td>{!! '&euro;&nbsp;' . number_format($zaagprijs, 2, ',', '.') !!}</td>
                                    <?php $totalToeslagPrice += $zaagprijs; ?>

                            </tr>
                            @endif
                        @endif
                    @endif
                @endforeach
            </table>
        @endif

        @if($order->comment)
            <table class="products toeslagen">
                <tr class="items">
                    <td><strong>{{ __('messages.Klant opmerking') }}</strong></td>
                    @if($order->orderRules)
                        <td>
                            <strong>Invloed op prijs</strong>
                        </td>
                    @endif
                </tr>
                <tr class="items">
                    <td>{{$order->comment}}</td>
                    @if($order?->status === 'Bevestigd' && $order?->orderRule)
                        <td>
                            &euro;&nbsp;{{ number_format($order->orderRule->price, 2, ',', '.') }}
                        </td>
                    @endif
                </tr>
            </table>

        @endif
    </div>


    <!-- Prijsblok (in normale flow) -->
    <div class="total" style="width: 100%; margin-left:auto; margin-top:50px;">
        <table class="total-table">
            <tr>
                <th style="text-align: left;">{{ __('messages.Totaal') }} m²:</th>
                <th style="text-align: left;"> m² {{$totalM2}}</th>
            </tr>
            <tr>
                <th style="text-align: left;">{{ __('messages.Subtotaal') }}:</th>
                <th style="text-align: left;">{!! '&euro;&nbsp;' . number_format($totalPrice, 2, ',', '.') !!}</th>
            </tr>
            <tr>
                <th style="text-align: left;">21% BTW:</th>
                <th style="text-align: left;">{!! '&euro;&nbsp;' . number_format($btw, 2, ',', '.') !!}</th>
            </tr>

            @if($zaaglengtes > 0 || $totalM2 < $vierkantemeterToeslag->number )
            <tr>
                <th style="text-align: left;">{{ __('messages.Toeslagen') }}:</th>
                <th style="text-align: left;">{!! '&euro;&nbsp;' . number_format($totalToeslagPrice, 2, ',', '.') !!}</th>
            </tr>
            @endif

            @if($order->orderRules)
                <tr>
                    <th style="text-align: left;">{{$order->orderRules->rule}}:</th>
                    <th style="text-align: left;">{!! '&euro;&nbsp;' . number_format($order->orderRules->price, 2, ',', '.') !!}</th>
                </tr>
            @endif
            <tr>
                <th style="text-align: left; border-top:1px solid black">
                    <strong>{{ __('messages.Totaal') }} incl. 21% BTW,  @if($zaaglengtes > 0 || $totalM2 < $vierkantemeterToeslag->number || $order->orderRules)incl. {{ __('messages.toeslagen') }}:@endif</strong>
                </th>

                @if($order->orderRules)
                    <th style="text-align: left; border-top:1px solid black">
                        € {{number_format($allInPrice + $totalToeslagPrice + $order->orderRules->price, 2, ',', '.')}}
                    </th>
                @else
                    <th style="text-align: left; border-top:1px solid black">
                        € {{number_format($allInPrice + $totalToeslagPrice, 2, ',', '.')}}
                    </th>
                @endif
            </tr>
        </table>
    </div>

    <!-- Footer altijd onderaan pagina -->
    <div class="footer" style="position: fixed; padding:15px; bottom: 0; left: 0; width: 100%; font-size: 0.75rem; line-height: 1.4; border-top: 1px solid #000; padding-top:5px;">
        <p><strong>{{ __('messages.Betalingsconditie') }}:</strong>{{ __('messages.14 dagen netto') }}</p>
        <p>
            {!!  __('messages.Op al onze offertes, adviezen, leveringen, opdrachten en op alle met ons gesloten overeenkomsten zijn onze voorwaarden van toepassing.<br> Deze voorwaarden worden op verzoek kosteloos toegezonden en zijn te lezen via uw portaal op <a href="https://my.rietpanel.com/" target="_blank">https://my.rietpanel.com/</a>.<br> Genoemde levertijden zijn verwachte levertijden en hieraan kunnen geen rechten worden ontleend.') !!}

        </p>
    </div>
</div>

<style>
    body, html {
        margin: 0;
        padding: 0;
        font-family: Arial, sans-serif;
        font-size: 0.875rem;
    }

    .w-full { width: 100%; }
    .w-half { width: 50%; }
    .margin-top { margin-top: 1.25rem; }

    /* Producten tabel */
    table.products {
        width: 100%;
        font-size: 0.875rem;
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
        border-top: 1px solid #000;
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
