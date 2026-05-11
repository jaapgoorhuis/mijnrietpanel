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
            <?php
            $totalPrice = 0;
            $zaaglengtes = 0;
            $laybacks = 0;
            $nokafschuining = 0;
            $vrijeruimte = 0;
            $count = 0;

            $zaaglengteToeslag = \App\Models\Surcharges::where('rule', 'zaaglengte')->first();
            ?>

            @foreach($orderLines as $orderLine)
                    <?php $count++; ?>

                <tr class="items">
                    <td>{{ $count }}</td>
                    <td>{{ $orderLine->fillTotaleLengte }} mm</td>

                    @if($showCb)
                        <td>{{ $orderLine->fillCb > 0 ? $orderLine->fillCb . ' mm' : '' }}</td>
                    @endif

                    @if($showLb)
                            <?php if($orderLine->lb > 0) $laybacks += $orderLine->aantal; ?>
                        <td>{{ $orderLine->lb > 0 ? $orderLine->lb . ' mm' : '' }}</td>
                    @endif

                    @if($showNokafschuining)
                            <?php if($orderLine->nokafschuining > 0) $nokafschuining += $orderLine->aantal; ?>
                        <td>{!! $orderLine->nokafschuining > 0 ? $orderLine->nokafschuining . ' &deg;' : '' !!}</td>
                    @endif

                    @if($showVrijeRuimte)
                            <?php if($orderLine->vrije_ruimte_2 > 0) $vrijeruimte += $orderLine->aantal; ?>
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
                            $panelTypeModel = \App\Models\PanelType::where('name', $order->kerndikte)->first();

                            if($company->is_reseller) {
                                $priceRule = \App\Models\PriceRules::where('panel_type', $panelTypeModel->id)
                                    ->where('company_id', $company->id)->first();
                                $discount = 0;
                            } else {
                                $priceRule = \App\Models\PriceRules::where('panel_type', $panelTypeModel->id)->first();
                                $discount = $priceRule->price / 100 * $company->discount;
                            }

                            $m2priceBeforeDiscount = $priceRule->price - $discount;
                            $orderDiscount = $m2priceBeforeDiscount / 100 * $order->discount;
                            $orderMarge = $m2priceBeforeDiscount / 100 * $order->marge;

                            if($company->is_reseller) {
                                $m2price = $m2priceBeforeDiscount - $orderDiscount;
                            } elseif($order->marge != 0) {
                                $m2price = $m2priceBeforeDiscount + $orderMarge;
                            } else {
                                $m2price = $m2priceBeforeDiscount;
                            }

                            $totalPrice += $orderLine->m2 * $m2price;

                            if($zaaglengteToeslag && $orderLine->fillTotaleLengte < $zaaglengteToeslag->number) {
                                $zaaglengtes += $orderLine->aantal;
                            }
                            ?>

                        {!! '&euro;&nbsp;' . number_format($orderLine->m2 * $m2price, 2, ',', '.') !!}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <?php
        $totalPriceWithouthSurchargesBtw = $totalPrice / 100 * 21;
        $toeslagen = \App\Models\Surcharges::get();

        $vierkantemeterToeslag = \App\Models\Surcharges::where('rule', 'vierkantemeter')->first();
        $vierkantemeterLimit = $vierkantemeterToeslag->number ?? null;

        $totalToeslagPrice = 0;
        $allInPrice = $totalPrice + $totalPriceWithouthSurchargesBtw;
        $totalM2 = $order->orderLines->sum('m2');

        $orderLineHeeftOversize = false;
        $oversizeThreshold = \App\Models\Surcharges::where('rule', 'order')->value('number');

        if ($oversizeThreshold) {
            foreach ($order->orderLines as $line) {
                if ($line->fillTotaleLengte > $oversizeThreshold) {
                    $orderLineHeeftOversize = true;
                    break;
                }
            }
        }

        // SAFE BOOLEANS (FIX)
        $hasZaag = $zaaglengtes > 0;
        $hasVierkant = $vierkantemeterLimit !== null && $totalM2 < $vierkantemeterLimit;
        $hasLb = $showLb && $laybacks > 0;
        $hasCb = $showCb;
        $hasNok = $showNokafschuining && $nokafschuining > 0;
        $hasVrije = $showVrijeRuimte && $vrijeruimte > 0;
        ?>

        {{-- ================= TOESLAGEN ================= --}}
        @if($hasZaag || $hasVierkant || $hasLb || $hasCb || $hasNok || $hasVrije || $orderLineHeeftOversize)

            <table class="products toeslagen">
                <tr class="items">
                    <td><strong>{{ __('messages.Toeslag') }}</strong></td>
                    <td><strong>{{ __('messages.Stuks') }}</strong></td>
                    <td><strong>{{ __('messages.Stuksprijs') }}</strong></td>
                    <td><strong>{{ __('messages.Totaal') }}</strong></td>
                </tr>

                @foreach($toeslagen as $toeslag)

                    @if($toeslag->rule == 'vierkantemeter' && $totalM2 < $toeslag->number)
                        <tr class="items">
                            <td>{{ __('messages.'.$toeslag->name) }}</td>
                            <td>1</td>
                            <td>{!! '&euro;&nbsp;' . number_format($toeslag->price, 2, ',', '.') !!}</td>
                            <td>{!! '&euro;&nbsp;' . number_format($toeslag->price, 2, ',', '.') !!}</td>
                        </tr>
                            <?php $totalToeslagPrice += $toeslag->price; ?>
                    @endif

                    @if($toeslag->rule == 'zaaglengte' && $zaaglengtes > 0)
                            <?php $zaagprijs = $zaaglengtes * $toeslag->price; ?>
                        <tr class="items">
                            <td>{{ __('messages.'.$toeslag->name) }}</td>
                            <td>{{ $zaaglengtes }}</td>
                            <td>{!! '&euro;&nbsp;' . number_format($toeslag->price, 2, ',', '.') !!}</td>
                            <td>{!! '&euro;&nbsp;' . number_format($zaagprijs, 2, ',', '.') !!}</td>
                        </tr>
                            <?php $totalToeslagPrice += $zaagprijs; ?>
                    @endif

                    @if($toeslag->rule == 'Layback' && $showLb)
                            <?php $totalLaybackPrice = $laybacks * $toeslag->price; ?>
                        <tr class="items">
                            <td>{{ __('messages.'.$toeslag->name) }}</td>
                            <td>{{ $laybacks }}</td>
                            <td>{!! '&euro;&nbsp;' . number_format($toeslag->price, 2, ',', '.') !!}</td>
                            <td>{!! '&euro;&nbsp;' . number_format($totalLaybackPrice, 2, ',', '.') !!}</td>
                        </tr>
                            <?php $totalToeslagPrice += $totalLaybackPrice; ?>
                    @endif

                    @if($toeslag->rule == 'order' && $orderLineHeeftOversize)
                        <tr class="items">
                            <td>{{ __('messages.'.$toeslag->name) }}</td>
                            <td>1</td>
                            <td>{!! '&euro;&nbsp;' . number_format($toeslag->price, 2, ',', '.') !!}</td>
                            <td>{!! '&euro;&nbsp;' . number_format($toeslag->price, 2, ',', '.') !!}</td>
                        </tr>
                            <?php $totalToeslagPrice += $toeslag->price; ?>
                    @endif

                    @if($toeslag->rule == 'Nokafschuining' && $showNokafschuining)
                            <?php $totalNokAfschuiningprice = $nokafschuining * $toeslag->price; ?>
                        <tr class="items">
                            <td>{{ __('messages.'.$toeslag->name) }}</td>
                            <td>{{ $nokafschuining }}</td>
                            <td>{!! '&euro;&nbsp;' . number_format($toeslag->price, 2, ',', '.') !!}</td>
                            <td>{!! '&euro;&nbsp;' . number_format($totalNokAfschuiningprice, 2, ',', '.') !!}</td>
                        </tr>
                            <?php $totalToeslagPrice += $totalNokAfschuiningprice; ?>
                    @endif

                    @if($toeslag->rule == 'Vrije ruimte' && $showVrijeRuimte)
                            <?php $vrijeruimteprice = $vrijeruimte * $toeslag->price; ?>
                        <tr class="items">
                            <td>{{ __('messages.'.$toeslag->name) }}</td>
                            <td>{{ $vrijeruimte }}</td>
                            <td>{!! '&euro;&nbsp;' . number_format($toeslag->price, 2, ',', '.') !!}</td>
                            <td>{!! '&euro;&nbsp;' . number_format($vrijeruimteprice, 2, ',', '.') !!}</td>
                        </tr>
                            <?php $totalToeslagPrice += $vrijeruimteprice; ?>
                    @endif

                @endforeach
            </table>
        @endif

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


                @if($hasZaag || $hasVierkant || $hasLb || $hasCb || $hasNok || $hasVrije || $orderLineHeeftOversize)
                    <tr>
                        <th style="text-align: left;">{{ __('messages.Toeslagen') }}:</th>
                        <th style="text-align: left;">{!! '&euro;&nbsp;' . number_format($totalToeslagPrice, 2, ',', '.') !!}</th>
                    </tr>

                        <?php
                        $totalToeslagPriceBtw = $totalToeslagPrice > 0
                            ? $totalToeslagPrice * 0.21
                            : 0;
                        ?>

                    <tr>
                        <th style="text-align: left;">21% BTW:</th>
                        <th style="text-align: left;">{!! '&euro;&nbsp;' . number_format($totalPriceWithouthSurchargesBtw + $totalToeslagPriceBtw, 2, ',', '.') !!}</th>
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
                        <strong>{{ __('messages.Totaal') }} incl. 21% BTW,    @if($hasZaag || $hasVierkant || $hasLb || $hasCb || $hasNok || $hasVrije || $orderLineHeeftOversize)
                                incl. {{ __('messages.toeslagen') }}:@endif</strong>
                    </th>

                    @if($order->orderRules)
                        <th style="text-align: left; border-top:1px solid black">
                            € {{number_format($allInPrice + $totalToeslagPrice + $totalToeslagPriceBtw+ $order->orderRules->price, 2, ',', '.')}}
                        </th>
                    @else
                        <th style="text-align: left; border-top:1px solid black">
                            € {{number_format($allInPrice + $totalToeslagPrice + $totalToeslagPriceBtw, 2, ',', '.')}}
                        </th>
                    @endif
                </tr>
            </table>
        </div>
    </div>

    <div class="footer" style="position: fixed; padding:15px; bottom: 0; left: 0; width: 100%; font-size: 0.75rem; line-height: 1.4; border-top: 1px solid #000; padding-top:5px;">
        <p><strong>{{ __('messages.Betalingsconditie') }}:</strong>{{ __('messages.14 dagen netto') }}</p>
        <p>{!! __('messages.orderConditions') !!}</p>
    </div>
</div>

<style>
    /* EXACT JOUW ORIGINELE CSS ONGEWIJZIGD */
    body, html { margin: 0; padding: 0; font-family: Arial, sans-serif; font-size: 0.8rem; }
    .w-full { width: 100%; }
    .w-half { width: 50%; }
    .margin-top { margin-top: 1.25rem; }

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
    table.products tr.items { background-color: #f9fafb; }
    table.products td { padding: 0.5rem 0.5rem 0.5rem 1rem; }

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
    table.toeslagen tr.items { background-color: #f9fafb; }
    table.toeslagen td { padding: 0.5rem 0.5rem 0.5rem 1rem; }

    .total-table {
        width: 50%;
        border-collapse: separate;
        border-spacing: 0 2px;
        font-weight: normal;
        margin-left: auto;
    }
    .total-table tr:last-child th {
        border-top: 1px solid #000;
        font-weight: bold;
    }

    img { max-width: 100%; height: auto; }
</style>

</body>
</html>
