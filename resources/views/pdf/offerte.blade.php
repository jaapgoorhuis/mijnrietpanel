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

$offerteLines = $offerteLines ?? $offerte->offerteLines;

$toeslagen = $offerte->surcharges ?? collect();
$hasToeslagen = $toeslagen->count() > 0;

$totalPrice = (float) ($offerte->subtotal ?? 0);
$totalToeslagPrice = (float) ($offerte->surcharges_total ?? 0);
$totalBtw = (float) ($offerte->vat_total ?? 0);
$grandTotal = (float) ($offerte->grand_total ?? 0);
$totalM2 = $offerteLines->sum('m2');

// Waterstops ophalen
$waterstopsByOfferteLine = collect();

if ($showWaterstop ?? false) {
    $waterstopsByOfferteLine = \Illuminate\Support\Facades\DB::table('offerte_line_waterstops')
        ->whereIn('offerte_line_id', $offerteLines->pluck('id'))
        ->orderBy('vertical')
        ->orderBy('horizontal')
        ->get()
        ->groupBy('offerte_line_id');
}
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
                    <div>{{$company->bedrijfsnaam}}</div>
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
                <td>@if($offerte->toepassing == 'Wand') Gevel @else Dak @endif</td>
                <td>{{$offerte->merk_paneel}}</td>
                <td>{{$offerte->kerndikte}}</td>
            </tr>
        </table>

        <h3>Panelen</h3>

        <table class="products elements-table">
            <thead>
            <tr>
                <th>#</th>
                <th>{{ __('messages.Lengte') }}</th>
                @if($showLb)<th>{{ __('messages.LB') }}</th>@endif
                @if($showCb)<th>{{ __('messages.CB') }}</th>@endif
                @if($showNokafschuining)<th>{{ __('messages.Nokafschuining') }}</th>@endif
                @if($showVrijeRuimte)<th>{{ __('messages.Vrije ruimte') }}</th>@endif
                @if($showWaterstop)<th>{{ __('messages.Waterstop') }}</th>@endif
                <th>m²</th>
                <th>{{ __('messages.Aantal') }}</th>
                <th>{{ __('messages.Prijs') }}</th>
            </tr>
            </thead>

            <tbody>
            <?php $count = 0; ?>

            @foreach($offerteLines as $line)
                    <?php
                    $count++;
                    $lineTotal = round(
                        (float) $line->m2 * (float) ($line->price_per_m2 ?? 0),
                        2
                    );
                    ?>
                <tr class="items">
                    <td>{{ $count }}</td>
                    <td>{{ $line->fillTotaleLengte }} mm</td>

                    @if($showCb)
                        <td>{{ $line->fillCb > 0 ? $line->fillCb . ' mm' : '' }}</td>
                    @endif
                    @if($showLb)
                        <td>{{ $line->lb > 0 ? $line->lb . ' mm' : '' }}</td>
                    @endif
                    @if($showNokafschuining)
                        <td>{!! $line->nokafschuining > 0 ? $line->nokafschuining . ' &deg;' : '' !!}</td>
                    @endif

                    @if($showVrijeRuimte)
                        <td>
                            {{ $line->vrije_ruimte_2 > 0
                                ? $line->vrije_ruimte_2 . ' mm (' . $line->vrije_ruimte_1 . ' ' . __('messages.mm vanaf boven') . ')'
                                : ''
                            }}
                        </td>
                    @endif

                    @if($showWaterstop)
                        <td class="waterstop-cell">
                            @php
                                $lineWaterstops = $waterstopsByOfferteLine[$line->id] ?? collect();

                                if ($lineWaterstops->isEmpty() && $line->waterstop_type) {
                                    $lineWaterstops = collect([
                                        (object) [
                                            'type' => $line->waterstop_type,
                                            'vertical' => $line->waterstop_vertical,
                                            'horizontal' => $line->waterstop_horizontal ?? 0,
                                        ]
                                    ]);
                                }
                            @endphp

                            @foreach($lineWaterstops as $waterstop)
                                <div class="waterstop-line">
                                    <strong>{{ $waterstop->type }} {{ __('messages.mm') }}</strong><br>
                                    {{ __('messages.V') }}: {{ $waterstop->vertical }} {{ __('messages.mm') }}<br>

                                    @if(($waterstop->horizontal ?? 0) < 0)
                                        {{ __('messages.H') }}: {{ abs($waterstop->horizontal) }} {{ __('messages.mm') }} {{ __('messages.naar links') }}
                                    @elseif(($waterstop->horizontal ?? 0) > 0)
                                        {{ __('messages.H') }}: {{ $waterstop->horizontal }} {{ __('messages.mm') }} {{ __('messages.naar rechts') }}
                                    @else
                                        {{ __('messages.Gecentreerd in het midden') }}
                                    @endif
                                </div>
                            @endforeach
                        </td>
                    @endif

                    <td>{{ $line->m2 }} m²</td>
                    <td>{{ $line->aantal }}</td>
                    <td>€ {{ number_format($lineTotal, 2, ',', '.') }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>

        @if($hasToeslagen)
            <table class="products toeslagen">
                <tr class="items">
                    <th>{{ __('messages.Toeslag') }}</th>
                    <th>{{ __('messages.Stuks') }}</th>
                    <th>{{ __('messages.Stuksprijs') }}</th>
                    <th>{{ __('messages.Totaal') }}</th>
                </tr>

                @foreach($toeslagen as $t)
                    <tr>
                        <td>{{ __('messages.' . $t->name) }}</td>
                        <td>{{ $t->qty }}</td>
                        <td>€ {{ number_format($t->unit_price, 2, ',', '.') }}</td>
                        <td>€ {{ number_format($t->total, 2, ',', '.') }}</td>
                    </tr>
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
                <th>{{ number_format($totalM2, 2, ',', '.') }} m²</th>
            </tr>

            <tr>
                <th>{{ __('messages.Subtotaal') }}:</th>
                <th>€ {{number_format($totalPrice,2,',','.')}}</th>
            </tr>

            @if($hasToeslagen)
                <tr>
                    <th>{{ __('messages.Toeslagen') }}:</th>
                    <th>€ {{number_format($totalToeslagPrice,2,',','.')}}</th>
                </tr>
            @endif

            <tr>
                <th>21% BTW:</th>
                <th>€ {{number_format($totalBtw,2,',','.')}}</th>
            </tr>

            @if($offerte->offerteRules)
                <tr>
                    <th>{{$offerte->offerteRules->rule}}</th>
                    <th>€ {{number_format($offerte->offerteRules->price,2,',','.')}}</th>
                </tr>
            @endif

            <tr>
                <th style="border-top: 1px solid #000;">
                    <strong>
                        {{ __('messages.Totaal') }} incl. 21% BTW
                        @if($hasToeslagen)
                            , incl. {{ __('messages.toeslagen') }}
                        @endif
                    </strong>
                </th>
                <th style="border-top: 1px solid #000;">
                    <strong>€ {{number_format($grandTotal,2,',','.')}}</strong>
                </th>
            </tr>
        </table>
    </div>

    <!-- FOOTER -->
    <div class="footer" style="position: fixed; padding:15px; bottom: 0; left: 0; width: 100%; font-size: 0.75rem; line-height: 1.4; border-top: 1px solid #000; padding-top:5px;">
        <p><strong>{{ __('messages.Betalingsconditie') }}:</strong> {{ __('messages.14 dagen netto') }}</p>
        <p>{!!  __('messages.orderConditions') !!}</p>
    </div>
</div>

<style>
    body, html { margin: 0; padding: 0; font-family: Arial, sans-serif; font-size: 0.8rem; }
    .w-full { width: 100%; }
    .w-half { width: 50%; }
    .margin-top { margin-top: 1.25rem; }

    table.products { width: 100%; font-size: 0.8rem; border-collapse: collapse; margin-top: 20px; }
    table.products th { background-color: #000; color: #fff; padding: 0.5rem; text-align: left; }
    table.products tr.items { background-color: #f9fafb; }
    table.products td { padding: 0.5rem 0.5rem 0.5rem 1rem; }

    table.elements-table { font-size: 0.72rem; }
    table.elements-table th, table.elements-table td { padding: 0.35rem; vertical-align: top; }

    .waterstop-cell { min-width: 80px; }
    .waterstop-line {
        border-bottom: 1px solid #e5e7eb;
        padding-bottom: 3px;
        margin-bottom: 3px;
        line-height: 1.25;
    }
    .waterstop-line:last-child { border-bottom: none; margin-bottom: 0; padding-bottom: 0; }

    table.toeslagen { width: 100%; margin-top: 25px; border-collapse: collapse; }
    table.toeslagen th { background-color: #000; color: #fff; padding: 0.5rem; text-align: left; }
    table.toeslagen td { padding: 0.5rem 0.5rem 0.5rem 1rem; }

    .total-table { width: 50%; border-collapse: separate; border-spacing: 0 2px; margin-left: auto; }
    .total-table th { padding: 2px 2px; text-align: left; }
    .total-table tr:last-child th { font-weight: bold; padding-top: 4px; }

    .footer {
        position: fixed; padding:15px; bottom: 0; left: 0; width: 100%;
        font-size: 0.75rem; line-height: 1.4; border-top: 1px solid #000; padding-top:5px;
    }
</style>
</body>
</html>
