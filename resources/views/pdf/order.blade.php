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
<table class="w-full">
    <tr>
        <td class="w-half">
            @if($order->user->companys->logo)
            <img src="{{ public_path("storage/companylogos/".$order->user->companys->logo)}}" alt="" style="width: 200px;"/>
            @else
                <img src="{{ public_path("storage/images/rietpanel_logo.png")}}" alt="" style="width: 200px;"/>
           @endif
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
                <div>Order datum:   {{date("d-m-Y", strtotime($order->created_at))}}</div><br/>
                <div>Project naam: {{$order->project_naam}}</div>
                <div>Referentie: {{$order->referentie}}</div>
                <div>Verkoper: {{$order->intaker}}</div>
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
                <div>Klant naam: {{$order->klant_naam}}</div>
                <div>Adres: {{$order->aflever_straat}}</div>
                <div>Postcode: {{$order->aflever_postcode}}</div>
                <div>Plaats: {{$order->aflever_plaats}}</div>
                <div>Land: {{$order->aflever_land}}</div>

            </td>
        </tr>
    </table>
</div>
<?php
$company = \App\Models\Company::where('id', $order->user->bedrijf_id)->first();


?>

<div class="margin-top">
    <table class="products">
        <tr>
            <th>Rietkleur</th>
            <th>Toepassing</th>
            <th>Merk</th>
            <th>CB</th>
            <th>LB</th>
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

        @foreach($orderLines as $key =>  $orderLine)
            {{$count++}}
            <tr class="items">
                <td>
                   {{$order->rietkleur}}
                </td>
                <td>
                    {{$order->toepassing}}
                </td>
                <td>
                    {{$order->merk_paneel}}
                </td>
                <td>
                    {{$orderLine->fillCb}}mm
                </td>
                <td>
                    {{$orderLine->fillLb}}mm
                </td>
                <td>
                    {{$order->kerndikte}}
                </td>
                <td>
                    {{$orderLine->fillTotaleLengte}}mm
                </td>
                <td>
                    {{$orderLine->m2}}m²
                </td>
                <td>
                    {{$orderLine->aantal}}
                </td>
                <td>
                    <?php
                        $panelType = \App\Models\PanelType::where('name', $order->kerndikte)->first();
                        if($company->is_reseller) {
                            $priceRule = \App\Models\PriceRules::where('panel_type', $panelType->id)->where('company_id', $company->id)->first();
                            $discount = 0;
                        }else {
                            $priceRule = \App\Models\PriceRules::where('panel_type', $panelType->id)->first();
                            $discount = $priceRule->price/100*$company->discount;
                        }

                        $m2priceBeforeDiscount = $priceRule->price - $discount;
                        $orderDiscount = $m2priceBeforeDiscount/100*$order->discount;
                        $m2price = $m2priceBeforeDiscount - $orderDiscount;

                        $totalPrice += $orderLine->m2 * $m2price;
                            if($zaaglengteToeslag) {
                                if($orderLine->fillTotaleLengte < $zaaglengteToeslag->number) {
                                    $zaaglengtes += $orderLine->aantal;
                                }
                            }
                        ?>
                    €{{str_replace('.', ',', round($orderLine->m2 * $m2price,2))}},-

                </td>
            </tr>
        @endforeach


    </table>
</div>

<div class="total">
    <?php $totalM2 = 0?>
    @foreach($order->orderLines as $orderLine)
            <?php $totalM2 += $orderLine->m2;?>
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
                    <th style="text-align: left; border-bottom:1px solid black">€{{str_replace('.', ',', round($totalPrice,2))}},-</th>
                </tr>
                <br/>
                <?php $btw = $totalPrice /100 *21?>
                <tr>
                    <th style="text-align: left">21% BTW:</th>
                    <th style="text-align: left">€{{str_replace('.', ',', round($btw,2))}},-</th>
                </tr>
                <?php $toeslagen = \App\Models\Surcharges::get();?>
                <?php $allInPrice = $totalPrice + $btw?>
                @foreach($toeslagen as $toeslag)
                    @if($toeslag)
                        @if($toeslag->rule == 'vierkantemeter')
                            @if( $totalM2 < $toeslag->number )
                                <tr>
                                    <th style="text-align: left; ">{{$toeslag->name}}:</th>
                                    <th style="text-align: left;">€ {{$toeslag->price}},-</th>
                                </tr>
                            @endif
                            <?php $allInPrice += $toeslag->price;?>
                        @endif

                        @if($toeslag->rule == 'zaaglengte')
                                <?php $zaagprijs = $zaaglengtes * $toeslag->price?>
                                <tr>
                                    <th style="text-align: left;  padding-right: 20px; margin-right: 20px;">{{$toeslag->name}}:</th>
                                    <th style="text-align: left">{{$zaaglengtes}} stuks * €{{$toeslag->price}},- = €{{$zaagprijs}},-</th>
                                </tr>
                                <?php $allInPrice += $zaagprijs?>
                            @endif

                        @endif

                @endforeach
                <br/>
                <tr>
                    <th style="text-align: left; border-top:1px solid black">Totaal incl. 21% BTW, incl. toeslagen:</th>
                    <th style="text-align: left; border-top:1px solid black">€{{str_replace('.', ',', round($allInPrice,2))}},-</th>
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
