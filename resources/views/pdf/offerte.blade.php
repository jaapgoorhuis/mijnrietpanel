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
<table class="w-full">
    <tr>
        <td class="w-half">
            @if($offerte->user->companys->logo)
                <img src="{{ public_path("storage/companylogos/".$offerte->user->companys->logo)}}" alt="" style="width: 200px;"/>
            @else
                <img src="{{ public_path("storage/images/rietpanel_logo.png")}}" alt="" style="width: 200px;"/>
            @endif

        </td>


    </tr>
</table>
<?php
$company = \App\Models\Company::where('id', $offerte->user->bedrijf_id)->first();


?>

<div class="margin-top">
    <table class="w-full">
        <tr>
            <td class="w-half">
                <div>Offerte #: {{$offerte->offerte_id}}</div>
                <div>Offerte datum:   {{date("d-m-Y", strtotime($offerte->created_at))}}</div><br/>
                <div>Referentie: {{$offerte->referentie}}</div>
                <div>Verkoper: {{$offerte->intaker}}</div>
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

    <table>
        <tr>
            <td class="w-half">
                <div><strong>Aan:</strong></div>
                <div>Klant naam: {{$offerte->klantnaam}}</div>
                <div>Adres: {{$offerte->aflever_straat}}</div>
                <div>Postcode: {{$offerte->aflever_postcode}}</div>
                <div>Plaats: {{$offerte->aflever_plaats}}</div>
                <div>Land: {{$offerte->aflever_land}}</div>

            </td>
        </tr>
    </table>
</div>


<div class="margin-top">
    <table class="products">
        <tr>
            <th>Rietkleur</th>
            <th>Toepassing</th>
            <th>Merk</th>
            <th>Kerndikte</th>
            <th>Lengte</th>
            <th>m²</th>
            <th>Aantal</th>
            <th>Prijs</th>
        </tr>
        <?php $totalPrice= 0?>
        <?php $zaaglengtes = 0?>
        <?php $count = 0;?>

        <?php  $zaaglengteToeslag = \App\Models\Surcharges::where('rule', 'zaaglengte')->first();?>

        @foreach($offerteLines as $key =>  $offerteLine)
            {{$count++}}
            <tr class="items">
                <td>
                   {{$offerte->rietkleur}}
                </td>
                <td>
                    {{$offerte->toepassing}}
                </td>
                <td>
                    {{$offerte->merk_paneel}}
                </td>
                <td>
                    {{$offerte->kerndikte}}
                </td>
                <td>
                    {{$offerteLine->fillTotaleLengte}} mm
                </td>
                <td>
                    {{$offerteLine->m2}} m²
                </td>
                <td>
                    {{$offerteLine->aantal}}
                </td>
                <td>
                    <?php
                        $panelType = \App\Models\PanelType::where('name', $offerte->kerndikte)->first();
                        if($company->is_reseller) {
                            $priceRule = \App\Models\PriceRules::where('panel_type', $panelType->id)->where('company_id', $company->id)->first();
                            $discount = 0;
                        }else {
                            $priceRule = \App\Models\PriceRules::where('panel_type', $panelType->id)->first();
                            $discount = $priceRule->price/100*$company->discount;
                        }

                        $m2priceBeforeDiscount = $priceRule->price - $discount;

                        $orderDiscount = $m2priceBeforeDiscount/100*$offerte->discount;
                        $orderMarge = $m2priceBeforeDiscount/100*$offerte->marge;
                        if($company->is_reseller) {
                            $m2price = $m2priceBeforeDiscount - $orderDiscount;
                        } else if($offerte->marge != 0) {
                            $m2price = $m2priceBeforeDiscount + $orderMarge;
                        } else {
                            $m2price = $m2priceBeforeDiscount;
                        }
                        $totalPrice += $offerteLine->m2 * $m2price;
                        if($zaaglengteToeslag) {
                            if($offerteLine->fillTotaleLengte < $zaaglengteToeslag->number) {
                                $zaaglengtes += $offerteLine->aantal;
                            }
                        }
                        ?>

                    {!! '&euro;&nbsp;' . number_format($offerteLine->m2 * $m2price, 2, ',', '.') !!}


                </td>
            </tr>
        @endforeach
        <?php $toeslagen = \App\Models\Surcharges::get();?>
        @foreach($toeslagen as $toeslag)
            @if($toeslag)


            @if($toeslag->rule == 'vierkantemeter')
                    @if( $totalM2 < $toeslag->number )
                        <tr class="items">
                            <th style="text-align: left; ">{{$toeslag->name}}:</th>
                            <th style="text-align: left;">{!! '&euro;&nbsp;' . number_format($toeslag->price, 2, ',', '.') !!}</th>
                        </tr>
                    @endif
                        <?php $allInPrice += $toeslag->price;?>
                @endif

                @if($toeslag->rule == 'zaaglengte')
                        <?php $zaagprijs = $zaaglengtes * $toeslag->price?>
                    <tr class="items">
                        <th style="text-align: left;">{{$toeslag->name}}:</th>
                        <th style="text-align: left">{{$zaaglengtes}} stuks * {!! '&euro;&nbsp;' . number_format($toeslag->price, 2, ',', '.') !!} = {!! '&euro;&nbsp;' . number_format($zaagprijs, 2, ',', '.') !!}</th>
                    </tr>
                        <?php $allInPrice += $zaagprijs?>
                @endif


            @endif
        @endforeach
    </table>
</div>

<div class="total">
    <?php $totalM2 = 0?>
    @foreach($offerte->offerteLines as $offerteLine)
            <?php $totalM2 += $offerteLine->m2;?>
    @endforeach

    <div class="totals-row">
        <div style="position:relative">
            <table class="total-table">
                <tr>
                    <th style="text-align: left; line-height: 35px; border-bottom:1px solid black">Totaal m²:</th>
                    <th style="line-height: 35px; text-align: left; border-bottom:1px solid black">{{$totalM2}} m²</th>
                </tr>
                <br/>
                <tr>
                    <th style="text-align: left; border-bottom:1px solid black">Subtotaal:</th>
                    <th style="text-align: left; border-bottom:1px solid black">{!! '&euro;&nbsp;' . number_format($totalPrice, 2, ',', '.') !!}</th>
                </tr>
                <br/>
                <?php $btw = $totalPrice /100 *21?>
                <tr>
                    <th style="text-align: left">21% BTW:</th>
                    <th style="text-align: left">{!! '&euro;&nbsp;' . number_format($btw, 2, ',', '.') !!}</th>
                </tr>

                <?php $allInPrice = $totalPrice + $btw?>

                <br/>
                <tr>
                    <th style="text-align: left; border-top:1px solid black">Totaal incl. 21% BTW, incl. toeslagen:</th>
                    <th style="text-align: left; border-top:1px solid black">{!! '&euro;&nbsp;' . number_format($allInPrice, 2, ',', '.') !!}</th>
                </tr>
            </table>
        </div>
    </div>

</div>
<style>
    h4 {
        margin: 0;
    }
    .w-full {
        width: 100%;
    }
    .w-half {
        width: 50%;
    }
    .margin-top {
        margin-top: 1.25rem;
    }

    table.total-table {
        width:100%;

    }
    .totals-row {
        position: absolute;
        bottom: 50px;
        right: 0;
    }

    table {
        width: 100%;
        border-spacing: 0;
    }
    table.products {
        font-size: 0.875rem;
    }
    table.products tr {
        background-color: black;
    }
    table.products th {
        color: #ffffff;
        padding:0.5rem;
    }
    table tr.items {
        background-color: #f9fafb;
    }
    table tr.items td {
        padding: 0.5rem 0.5rem 0.5rem 1rem;
    }
    .total {
        text-align: right;
        margin-top: 1rem;
        font-size: 0.875rem;
    }

</style>
</body>
</html>
