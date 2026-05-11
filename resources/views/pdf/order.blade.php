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
    $company = \App\Models\Company::where('id', $order->user->bedrijf_id ?? null)->first();
    ?>

    <div class="margin-top">
        <table class="w-full">
            <tr>
                <td class="w-half">
                    <div>Order #: {{$order->order_id}}</div>
                    <div>{{ __('messages.Order datum') }}: {{date("d-m-Y", strtotime($order->created_at))}}</div><br/>
                    <div>{{ __('messages.Bedrijfs-id') }}: {{$company->id ?? ''}}</div>

                    <div>{{ __('messages.Referentie') }}: {{$order->referentie}}</div>
                    <div>{{ __('messages.Verkoper') }}: {{$order->intaker}}</div>

                    @if($order->status == 'In behandeling')
                        <div>{{ __('messages.Gewenste afleverdatum') }}: {{$order->requested_delivery_date}}</div>
                    @else
                        <div>{{ __('messages.Afleverdatum') }}: {{$order->delivery_date}}</div>
                    @endif
                </td>

                <td class="w-half">
                    <div>{{$company->straat ?? ''}}</div>
                    <div>{{$company->postcode ?? ''}}</div>
                    <div>{{$company->plaats ?? ''}}</div>
                    <div>M: {{$order->user->email ?? ''}}</div>
                    <div>T: {{$order->user->phone ?? ''}}</div>
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

            $panelTypeModel = \App\Models\PanelType::where('name', $order->kerndikte)->first();
            $priceRuleGlobal = null;

            if ($panelTypeModel) {
                $priceRuleGlobal = \App\Models\PriceRules::where('panel_type', $panelTypeModel->id)->first();
            }

            $totalPrice = 0;
            ?>

            @foreach($orderLines as $orderLine)
                    <?php $count++; ?>

                <tr class="items">
                    <td>{{ $count }}</td>
                    <td>{{ $orderLine->fillTotaleLengte }} mm</td>

                    @if($showCb)
                        <td>{{ $orderLine->fillCb > 0 ? $orderLine->fillCb.' mm' : '' }}</td>
                    @endif

                    @if($showLb)
                            <?php if($orderLine->lb > 0) $laybacks += $orderLine->aantal; ?>
                        <td>{{ $orderLine->lb > 0 ? $orderLine->lb.' mm' : '' }}</td>
                    @endif

                    @if($showNokafschuining)
                            <?php if($orderLine->nokafschuining > 0) $nokafschuining += $orderLine->aantal; ?>
                        <td>{!! $orderLine->nokafschuining > 0 ? $orderLine->nokafschuining.' &deg;' : '' !!}</td>
                    @endif

                    @if($showVrijeRuimte)
                            <?php if($orderLine->vrije_ruimte_2 > 0) $vrijeruimte += $orderLine->aantal; ?>
                        <td>
                            {{ $orderLine->vrije_ruimte_2 > 0
                                ? $orderLine->vrije_ruimte_2.' mm'
                                : '' }}
                        </td>
                    @endif

                    <td>{{ $orderLine->m2 }} m²</td>
                    <td>{{ $orderLine->aantal }}</td>

                    <td>
                            <?php
                            $m2price = $priceRuleGlobal->price ?? 0;
                            $lineTotal = $orderLine->m2 * $m2price;
                            $totalPrice += $lineTotal;
                            ?>

                        {!! '&euro;&nbsp;' . number_format($lineTotal, 2, ',', '.') !!}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <?php
        $totalM2 = $order->orderLines->sum('m2');

        $vierkantemeterToeslag = \App\Models\Surcharges::where('rule', 'vierkantemeter')->first();
        $vierkantemeterLimit = $vierkantemeterToeslag->number ?? null;

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

        $hasZaag = $zaaglengtes > 0;
        $hasVierkant = $vierkantemeterLimit && $totalM2 < $vierkantemeterLimit;
        $hasLb = !empty($showLb) && $laybacks > 0;
        $hasCb = !empty($showCb);
        $hasNok = !empty($showNokafschuining) && $nokafschuining > 0;
        $hasVrije = !empty($showVrijeRuimte) && $vrijeruimte > 0;
        ?>

        @if($hasZaag || $hasVierkant || $hasLb || $hasCb || $hasNok || $hasVrije || $orderLineHeeftOversize)

            <table class="products toeslagen">
                <tr class="items">
                    <td><strong>{{ __('messages.Toeslag') }}</strong></td>
                    <td><strong>{{ __('messages.Stuks') }}</strong></td>
                    <td><strong>{{ __('messages.Stuksprijs') }}</strong></td>
                    <td><strong>{{ __('messages.Totaal') }}</strong></td>
                </tr>

                @foreach($toeslagen as $toeslag)
                    @if($toeslag)
                        <tr class="items">
                            <td>{{ __('messages.'.$toeslag->name) }}</td>
                            <td>1</td>
                            <td>{!! '&euro;&nbsp;' . number_format($toeslag->price, 2, ',', '.') !!}</td>
                            <td>{!! '&euro;&nbsp;' . number_format($toeslag->price, 2, ',', '.') !!}</td>
                        </tr>
                    @endif
                @endforeach
            </table>

        @endif

    </div>

    <style>
        body, html { margin:0; padding:0; font-family: Arial, sans-serif; font-size:0.8rem; }
        .w-full { width:100%; }
        .w-half { width:50%; }
        .margin-top { margin-top:1.25rem; }

        table.products {
            width:100%;
            border-collapse:collapse;
            margin-top:20px;
        }

        table.products th {
            background:#000;
            color:#fff;
            padding:0.5rem;
        }

        table.products td {
            padding:0.5rem;
        }

        table.products tr.items {
            background:#f9fafb;
        }
    </style>

</div>
</body>
</html>
