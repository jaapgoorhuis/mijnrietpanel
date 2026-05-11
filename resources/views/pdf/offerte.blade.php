<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Offerte</title>
    <link rel="stylesheet" href="{{ asset('pdf.css') }}" type="text/css">
</head>
<body>

<?php
$company = \App\Models\Company::where('id', $offerte->user->bedrijf_id)->first();
?>

<div class="page-container" style="padding:15px;">

    <table class="w-full">
        <tr>
            <td class="w-half">
                @if($company->logo)
                    <img src="{{ public_path("storage/companylogos/".$company->logo)}}" style="width:200px;">
                @else
                    <img src="{{ public_path("storage/images/rietpanel_logo.png")}}" style="width:200px;">
                @endif
            </td>
        </tr>
    </table>

    <div class="margin-top">
        <table class="w-full">
            <tr>
                <td class="w-half">
                    <div>Offerte #: {{$offerte->offerte_id}}</div>
                    <div>{{ __('messages.Offerte datum') }}: {{date("d-m-Y", strtotime($offerte->created_at))}}</div>
                    <div>{{ __('messages.Project naam') }}: {{$offerte->project_naam}}</div>
                    <div>{{ __('messages.Referentie') }}: {{$offerte->referentie}}</div>
                    <div>{{ __('messages.Verkoper') }}: {{$offerte->intaker}}</div>

                    @if($offerte->status == 'In behandeling')
                        <div>{{ __('messages.Gewenste afleverdatum') }}: {{$offerte->requested_delivery_date}}</div>
                    @else
                        <div>{{ __('messages.Afleverdatum') }}: {{$offerte->delivery_date}}</div>
                    @endif
                </td>
                <td class="w-half">
                    <div>{{$company->straat}}</div>
                    <div>{{$company->postcode}}</div>
                    <div>{{$company->plaats}}</div>
                    <div>M: {{$offerte->user->email}}</div>
                    <div>T: {{$offerte->user->phone}}</div>
                </td>
            </tr>
        </table>

        <br>

        <div>
            <strong>Aan:</strong>
            <div>{{ __('messages.Klant naam') }}: {{$offerte->klantnaam}}</div>
            <div>{{ __('messages.Adres') }}: {{$offerte->aflever_straat}}</div>
            <div>{{ __('messages.Postcode') }}: {{$offerte->aflever_postcode}}</div>
            <div>{{ __('messages.Plaats') }}: {{$offerte->aflever_plaats}}</div>
            <div>{{ __('messages.Land') }}: {{$offerte->aflever_land}}</div>
        </div>
    </div>

    <div class="margin-top">

        <h3>Offerte</h3>

        <table class="products">
            <tr>
                <th>{{ __('messages.Rietkleur') }}</th>
                <th>{{ __('messages.Toepassing') }}</th>
                <th>{{ __('messages.Merk') }}</th>
                <th>{{ __('messages.Kerndikte') }}</th>
            </tr>
            <tr class="items">
                <td>{{$offerte->rietkleur}}</td>
                <td>{{$offerte->toepassing}}</td>
                <td>{{$offerte->merk_paneel}}</td>
                <td>{{$offerte->kerndikte}}</td>
            </tr>
        </table>

        <h3>Panelen</h3>

        <table class="products">
            <thead>
            <tr>
                <th>#</th>
                <th>{{ __('messages.Lengte') }}</th>

                @if($showCb)<th>{{ __('messages.CB') }}</th>@endif
                @if($showLb)<th>{{ __('messages.LB') }}</th>@endif
                @if($showNokafschuining)<th>{{ __('messages.Nokafschuining') }}</th>@endif
                @if($showVrijeRuimte)<th>{{ __('messages.Vrije ruimte') }}</th>@endif

                <th>m²</th>
                <th>{{ __('messages.Aantal') }}</th>
                <th>{{ __('messages.Prijs') }}</th>
            </tr>
            </thead>

            <tbody>
            <?php
            $totalPrice = 0;
            $zaaglengtes = 0;
            $laybacks = 0;
            $nokafschuining = 0;
            $vrijeruimte = 0;
            $count = 0;

            $zaaglengteToeslag = \App\Models\Surcharges::where('rule', 'zaaglengte')->first();
            ?>

            @foreach($offerteLines as $line)
                    <?php $count++; ?>

                <tr class="items">
                    <td>{{ $count }}</td>
                    <td>{{ $line->fillTotaleLengte }} mm</td>

                    @if($showCb)
                        <td>{{ $line->fillCb > 0 ? $line->fillCb.' mm' : '' }}</td>
                    @endif

                    @if($showLb)
                            <?php if($line->lb > 0) $laybacks += $line->aantal; ?>
                        <td>{{ $line->lb > 0 ? $line->lb.' mm' : '' }}</td>
                    @endif

                    @if($showNokafschuining)
                            <?php if($line->nokafschuining > 0) $nokafschuining += $line->aantal; ?>
                        <td>{{ $line->nokafschuining > 0 ? $line->nokafschuining.'°' : '' }}</td>
                    @endif

                    @if($showVrijeRuimte)
                            <?php if($line->vrije_ruimte_2 > 0) $vrijeruimte += $line->aantal; ?>
                        <td>
                            {{ $line->vrije_ruimte_2 > 0
                                ? $line->vrije_ruimte_2.' mm ('.$line->vrije_ruimte_1.' mm vanaf boven)'
                                : '' }}
                        </td>
                    @endif

                    <td>{{ $line->m2 }} m²</td>
                    <td>{{ $line->aantal }}</td>

                    <td>
                            <?php
                            $panelTypeModel = \App\Models\PanelType::where('name', $offerte->kerndikte)->first();

                            if($company->is_reseller){
                                $priceRule = \App\Models\PriceRules::where('panel_type', $panelTypeModel->id)
                                    ->where('company_id', $company->id)->first();
                                $discount = 0;
                            } else {
                                $priceRule = \App\Models\PriceRules::where('panel_type', $panelTypeModel->id)->first();
                                $discount = $priceRule->price / 100 * $company->discount;
                            }

                            $base = $priceRule->price - $discount;
                            $disc = $base / 100 * $offerte->discount;
                            $marge = $base / 100 * $offerte->marge;

                            $m2price = $company->is_reseller
                                ? $base - $disc
                                : ($offerte->marge != 0 ? $base + $marge : $base);

                            $lineTotal = $line->m2 * $m2price;
                            $totalPrice += $lineTotal;

                            if($zaaglengteToeslag && $line->fillTotaleLengte < $zaaglengteToeslag->number){
                                $zaaglengtes += $line->aantal;
                            }
                            ?>

                        € {{ number_format($lineTotal, 2, ',', '.') }}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <?php
        $btw = $totalPrice * 0.21;
        $toeslagen = \App\Models\Surcharges::get();
        $vierkantemeterToeslag = \App\Models\Surcharges::where('rule', 'vierkantemeter')->first();

        $totalToeslagPrice = 0;
        $totalM2 = $offerte->offerteLines->sum('m2');

        $orderLineHeeftOversize = false;
        $oversizeThreshold = \App\Models\Surcharges::where('rule', 'order')->value('number');

        foreach($offerte->offerteLines as $l){
            if($oversizeThreshold && $l->fillTotaleLengte > $oversizeThreshold){
                $orderLineHeeftOversize = true;
                break;
            }
        }

        $allInPrice = $totalPrice + $btw;
        ?>

        @if($zaaglengtes > 0 || $totalM2 < $vierkantemeterToeslag->number || $laybacks || $nokafschuining || $vrijeruimte || $orderLineHeeftOversize)

            <table class="products toeslagen">
                <tr class="items">
                    <th>{{ __('messages.Toeslag') }}</th>
                    <th>{{ __('messages.Stuks') }}</th>
                    <th>{{ __('messages.Stuksprijs') }}</th>
                    <th>{{ __('messages.Totaal') }}</th>
                </tr>

                @foreach($toeslagen as $t)

                    @if($t->rule == 'zaaglengte' && $zaaglengtes > 0)
                            <?php $price = $zaaglengtes * $t->price; $totalToeslagPrice += $price; ?>
                        <tr>
                            <td>{{$t->name}}</td>
                            <td>{{$zaaglengtes}}</td>
                            <td>€ {{number_format($t->price,2,',','.')}}</td>
                            <td>€ {{number_format($price,2,',','.')}}</td>
                        </tr>
                    @endif

                    @if($t->rule == 'Layback' && $showLb)
                            <?php $price = $laybacks * $t->price; $totalToeslagPrice += $price; ?>
                        <tr>
                            <td>{{$t->name}}</td>
                            <td>{{$laybacks}}</td>
                            <td>€ {{number_format($t->price,2,',','.')}}</td>
                            <td>€ {{number_format($price,2,',','.')}}</td>
                        </tr>
                    @endif

                    @if($t->rule == 'Nokafschuining' && $showNokafschuining)
                            <?php $price = $nokafschuining * $t->price; $totalToeslagPrice += $price; ?>
                        <tr>
                            <td>{{$t->name}}</td>
                            <td>{{$nokafschuining}}</td>
                            <td>€ {{number_format($t->price,2,',','.')}}</td>
                            <td>€ {{number_format($price,2,',','.')}}</td>
                        </tr>
                    @endif

                    @if($t->rule == 'Vrije ruimte' && $showVrijeRuimte)
                            <?php $price = $vrijeruimte * $t->price; $totalToeslagPrice += $price; ?>
                        <tr>
                            <td>{{$t->name}}</td>
                            <td>{{$vrijeruimte}}</td>
                            <td>€ {{number_format($t->price,2,',','.')}}</td>
                            <td>€ {{number_format($price,2,',','.')}}</td>
                        </tr>
                    @endif

                    @if($t->rule == 'vierkantemeter' && $totalM2 < $t->number)
                            <?php $totalToeslagPrice += $t->price; ?>
                        <tr>
                            <td>{{$t->name}}</td>
                            <td>1</td>
                            <td>€ {{number_format($t->price,2,',','.')}}</td>
                            <td>€ {{number_format($t->price,2,',','.')}}</td>
                        </tr>
                    @endif

                    @if($t->rule == 'order' && $orderLineHeeftOversize)
                            <?php $totalToeslagPrice += $t->price; ?>
                        <tr>
                            <td>{{$t->name}}</td>
                            <td>1</td>
                            <td>€ {{number_format($t->price,2,',','.')}}</td>
                            <td>€ {{number_format($t->price,2,',','.')}}</td>
                        </tr>
                    @endif

                @endforeach
            </table>
        @endif

        @if($offerte->comment)
            <table class="products toeslagen">
                <tr>
                    <th>{{ __('messages.Klant opmerking') }}</th>
                </tr>
                <tr>
                    <td>{{$offerte->comment}}</td>
                </tr>
            </table>
        @endif
    </div>

    <!-- TOTAL -->
    <div class="total" style="margin-top:50px;">
        <table class="total-table">

            <tr>
                <th>{{ __('messages.Totaal') }} m²:</th>
                <th>{{$totalM2}}</th>
            </tr>

            <tr>
                <th>{{ __('messages.Subtotaal') }}:</th>
                <th>€ {{number_format($totalPrice,2,',','.')}}</th>
            </tr>

            @if($totalToeslagPrice > 0)
                <tr>
                    <th>{{ __('messages.Toeslagen') }}:</th>
                    <th>€ {{number_format($totalToeslagPrice,2,',','.')}}</th>
                </tr>
            @endif

            <tr>
                <th>21% BTW:</th>
                <th>€ {{number_format($btw + ($totalToeslagPrice * 0.21),2,',','.')}}</th>
            </tr>

            @if($offerte->offerteRules)
                <tr>
                    <th>{{$offerte->offerteRules->rule}}</th>
                    <th>€ {{number_format($offerte->offerteRules->price,2,',','.')}}</th>
                </tr>
            @endif

            <tr>
                <th style="border-top:1px solid #000;"><strong>Totaal</strong></th>
                <th style="border-top:1px solid #000;">
                    € {{
                        number_format(
                            $allInPrice
                            + $totalToeslagPrice
                            + ($totalToeslagPrice * 0.21)
                            + ($offerte->offerteRules->price ?? 0)
                        ,2,',','.')
                    }}
                </th>
            </tr>

        </table>
    </div>

    <!-- FOOTER -->
    <div class="footer" style="position:fixed;bottom:0;width:100%;padding:15px;border-top:1px solid #000;">
        <strong>{{ __('messages.Betalingsconditie') }}:</strong> {{ __('messages.14 dagen netto') }}
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
