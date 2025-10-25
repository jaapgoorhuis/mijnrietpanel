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
<div class="page-container" style="padding:15px;">
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
                    <div>Offerte datum: {{date("d-m-Y", strtotime($offerte->created_at))}}</div><br/>
                    <div>Project naam: {{$offerte->project_naam}}</div>
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

        <br/>

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

            <?php $totalPrice= 0 ?>
            <?php $zaaglengtes = 0 ?>
            <?php $count = 0; ?>
            <?php $zaaglengteToeslag = \App\Models\Surcharges::where('rule', 'zaaglengte')->first(); ?>

            @foreach($offerteLines as $key =>  $offerteLine)
                {{$count++}}
                <tr class="items">
                    <td>{{$offerte->rietkleur}}</td>
                    <td>{{$offerte->toepassing}}</td>
                    <td>{{$offerte->merk_paneel}}</td>
                    <td>{{$offerte->kerndikte}}</td>
                    <td>{{$offerteLine->fillTotaleLengte}} mm</td>
                    <td>{{$offerteLine->m2}} m²</td>
                    <td>{{$offerteLine->aantal}}</td>
                    <td>
                            <?php
                            $panelType = \App\Models\PanelType::where('name', $offerte->kerndikte)->first();
                            if($company->is_reseller) {
                                $priceRule = \App\Models\PriceRules::where('panel_type', $panelType->id)->where('company_id', $company->id)->first();
                                $discount = 0;
                            } else {
                                $priceRule = \App\Models\PriceRules::where('panel_type', $panelType->id)->first();
                                $discount = $priceRule->price/100*$company->discount;
                            }

                            $m2priceBeforeDiscount = $priceRule->price - $discount;
                            $offerteDiscount = $m2priceBeforeDiscount/100*$offerte->discount;
                            $offerteMarge = $m2priceBeforeDiscount/100*$offerte->marge;

                            if($company->is_reseller) {
                                $m2price = $m2priceBeforeDiscount - $offerteDiscount;
                            } else if($offerte->marge != 0) {
                                $m2price = $m2priceBeforeDiscount + $offerteMarge;
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
        </table>

        <?php $btw = $totalPrice /100 *21 ?>
        <?php $toeslagen = \App\Models\Surcharges::get(); ?>
        <?php $allInPrice = $totalPrice + $btw ?>
        <?php $totalM2 = 0 ?>
        <?php $totalToeslagPrice = 0?>

        @foreach($offerte->offerteLines as $offerteLine)
                <?php $totalM2 += $offerteLine->m2;?>
        @endforeach

        @if(count($toeslagen))
            <table class="products toeslagen">
                <tr class="items">
                    <td><strong>Toeslag</strong></td>
                    <td><strong>Stuks</strong></td>
                    <td><strong>Stukprijs</strong></td>
                    <td><strong>Totaal</strong></td>
                </tr>

                @foreach($toeslagen as $toeslag)
                    @if($toeslag)
                        @if($toeslag->rule == 'vierkantemeter')
                            <tr class="items">
                                @if($totalM2 < $toeslag->number)
                                    <td>{{$toeslag->name}}</td>
                                    <td>1</td>
                                    <td>{!! '&euro;&nbsp;' . number_format($toeslag->price, 2, ',', '.') !!}</td>
                                    <td>{!! '&euro;&nbsp;' . number_format($toeslag->price, 2, ',', '.') !!}</td>
                                @endif
                                    <?php $allInPrice += $toeslag->price; ?>
                                    <?php $totalToeslagPrice += $toeslag->price; ?>

                            </tr>
                        @endif

                        @if($toeslag->rule == 'zaaglengte')
                            @if($zaaglengtes > 0)
                                <tr class="items">
                                        <?php $zaagprijs = $zaaglengtes * $toeslag->price ?>
                                    <td>{{$toeslag->name}}</td>
                                    <td>{{$zaaglengtes}}</td>
                                    <td>{!! '&euro;&nbsp;' . number_format($toeslag->price, 2, ',', '.') !!}</td>
                                    <td>{!! '&euro;&nbsp;' . number_format($zaagprijs, 2, ',', '.') !!}</td>
                                        <?php $allInPrice += $zaagprijs ?>
                                        <?php $totalToeslagPrice += $toeslag->price; ?>

                                </tr>
                                @endif
                        @endif
                    @endif
                @endforeach
            </table>
        @endif
    </div>


    <!-- Prijsblok (in normale flow) -->
    <div class="total" style="width: 100%; margin-left:auto; margin-top:50px;">
        <table class="total-table">
            <tr>
                <th style="text-align: left;">Totaal m²:</th>
                <th style="text-align: left;"> m² {{$totalM2}}</th>
            </tr>
            <tr>
                <th style="text-align: left;">Subtotaal:</th>
                <th style="text-align: left;">{!! '&euro;&nbsp;' . number_format($totalPrice, 2, ',', '.') !!}</th>
            </tr>
            <tr>
                <th style="text-align: left;">21% BTW:</th>
                <th style="text-align: left;">{!! '&euro;&nbsp;' . number_format($btw, 2, ',', '.') !!}</th>
            </tr>
            @if(count($toeslagen))
                <tr>
                    <th style="text-align: left;">Toeslagen:</th>
                    <th style="text-align: left;">{!! '&euro;&nbsp;' . number_format($totalToeslagPrice, 2, ',', '.') !!}</th>
                </tr>
            @endif
            <tr>
                <th style="text-align: left; border-top:1px solid black">
                    <strong>Totaal incl. 21% BTW, @if(count($toeslagen))incl. toeslagen:@endif</strong>
                </th>
                <th style="text-align: left; border-top:1px solid black">
                    € {{number_format($allInPrice, 2, ',', '.')}}
                </th>
            </tr>
        </table>
    </div>

    <!-- Footer altijd onderaan pagina -->
    <div class="footer" style="position: fixed; padding:15px; bottom: 0; left: 0; width: 100%; font-size: 0.75rem; line-height: 1.4; border-top: 1px solid #000; padding-top:5px;">
        <p><strong>Betalingsconditie:</strong> 14 dagen netto</p>
        <p>
            Op al onze offertes, adviezen, leveringen, opdrachten en op alle met ons gesloten overeenkomsten zijn onze voorwaarden van toepassing.<br>
            Deze voorwaarden worden op verzoek kosteloos toegezonden en zijn te lezen via uw portaal op
            <a href="https://mijn.rietpanel.nl/" target="_blank">https://mijn.rietpanel.nl/</a>.<br>
            Genoemde levertijden zijn verwachte levertijden en hieraan kunnen geen rechten worden ontleend.
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
