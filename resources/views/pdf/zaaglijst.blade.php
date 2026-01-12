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
<div class="margin-top">
    <table class="text">
        <tr>
            <td class="label">Ordernummer:</td>
            <td>25001</td>
            <td class="label">Dikte paneel:</td>
            <td style="color:red">{{$order->kerndikte}}</td>
            <td rowspan="6" class="logo">
                <img src="{{ public_path("storage/images/rietpanel_logo.png")}}" alt="" style="width: 200px;"/>
            </td>
        </tr>
        <tr>
            <td class="label">Klantnaam:</td>
            <td>{{$order->klantnaam}}</td>
            <td class="label">Riet spoort:</td>
            <td  style="color:red">{{$order->rietkleur}}</td>
        </tr>
        <tr>
            <td class="label">Projectnaam:</td>
            <td>{{$order->project_naam}}</td>
            <td class="label">Toepassing:</td>
            <td  style="color:red">{{$order->toepassing}}</td>
        </tr>
        <tr>
            <td class="label">Aanmaakdatum:</td>
            <td>{{$order->updated_at->format('d-m-Y')}}</td>
            <td class="label">Merk paneel:</td>
            <td  style="color:red">{{$order->merk_paneel}}</td>
        </tr>

        <tr>
            <td class="label">Totale m²:</td>
            <td>
              {{$order->orderlines()->sum('m2')}} m²
            </td>
            <td class="label">Artikelnummer:</td>
            <td  style="color:red">
                RP{{str_replace('m', '', $order->kerndikte)}}-{{strtoupper(substr($order->merk_paneel, 0, 2))}}-{{strtoupper(substr($order->rietkleur, 0, 1))}}
            </td>
        </tr>
        <tr>
            <td>Leverdatum:</td>
            <td >{{$order->delivery_date}}</td>
            <td></td>
            <td></td>
        </tr>
    </table>

    @if($order->orderRules)
    <table class="order-table-visual">
        <tr>
            <td class="header">Opmerkingen</td>
            <td style="color:red; font-weight: bold">{{$order->orderRules->rule}}</td>
        </tr>
    </table>
    @endif

    <table class="order-table-visual">
    @foreach($order->orderLines->sortByDesc('fillTotaleLengte')->values() as $key => $orderLines)
        <tr>
            <td class="header">Order regel {{$key+1}}</td>
            <td class="right">
                <span class="aantal">Aantal: <span style="color: red;">{{$orderLines->aantal}}</span></span>
            </td>
        </tr>
            <tr class="bar-row">
                <td colspan="2">
                    <div class="panel-banner" style="background-image: url('/public/storage/images/rietpanel_panel.png');">
                        <div class="cb-label">
                            <strong>CB:</strong>{{$orderLines->fillCb}} mm
                        </div>

{{--                        <div class="lb-label">--}}
{{--                            <strong>LB:</strong> {{$orderLines->fillLb}} mm--}}
{{--                        </div>--}}

                        <div class="totale-maat" style="margin-bottom: 10px;">
                            <strong><- Totale maat: </strong> {{$orderLines->fillTotaleLengte}}
                        </div>
                    </div>
                </td>
            </tr>
        @endforeach
    </table>


</div>
<style>
    body {
        margin: 0;
        padding: 20px;
        text-align: center;
    }

    table.text {
        font-weight: bold;
    }
    tr.head> {
        font-weight:bold;
    }
    table {
        text-align:left;
        margin: 0 auto; /* Horizontaal centreren */
        border-collapse: collapse;
        width: 100%;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    td {
        border: 1px solid black;
        padding: 3px;
        vertical-align: top;
    }

    .label {
        text-align:right;
        font-weight: bold;

    }

    .logo {
        text-align: center;
        width: 100px;
    }

    .order-table-visual {
        border-collapse: collapse;
        width: 100%;
        border: 1px solid black;
    }

    .order-table-visual td {
        padding: 5px;
        vertical-align: top;
    }

    .order-table-visual .header {
        font-weight: bold;
    }

    .order-table-visual .right {
        text-align: right;
    }

    .order-table-visual .aantal {
        border: 1px solid black;
        padding: 3px 8px;
    }

    .order-table-visual .bar-row {
        height: 80px;
        position: relative;
    }

    .order-table-visual .bar-container {
        position: relative;
        height: 85px;
    }

    .order-table-visual .scale-line {
        border-top: 1px solid black;
        width: 100%;
        position: absolute;
        top: 20px;
    }

    .order-table-visual .yellow-bar {
        background-color: #fdd76e;
        height: 20px;
        width: 80%;
        position: absolute;
        top: 20px;
    }

    .order-table-visual .gray-bar {
        background-color: #555;
        height: 20px;
        width: 80%;
        position: absolute;
        top: 45px;
        left: 110px;
    }

    .order-table-visual .red-label {
        color: red;
        font-weight: bold;
        position: absolute;
    }

    .order-table-visual .label-3000 {
        top: 0;
        left: 45%;
    }

    .order-table-visual .label-110 {
        top: 45px;
        left: 0;
    }

    .order-table-visual .label-0 {
        top: 20px;
        right: 30px;
    }

    .line-container {
        position: relative;
        width: 97%;
        bottom: 15px;
        margin-top: 25px;
    }

    .line-label {
        position: absolute;
        top: 0;
        left: 50%;
        transform: translateX(-50%);
        color: red;
        font-weight: bold;
        font-size: 16px;
    }

    .horizontal-line {
        position: absolute;
        top: 20px;
        left: 0;
        right: 0;
        height: 1px;
        background-color: black;
    }

    .vertical-end-left,
    .vertical-end-right {
        position: absolute;
        width: 1px;
        height: 20px;
        background-color: black;
        top: 10px;
    }

    .vertical-end-left {
        left: 0;
    }

    .vertical-end-right {
        right: 0;
    }

    .panel-banner {
        position: relative;
        width: 100%;
        height: 120px;

        background-size: contain;
        background-repeat: no-repeat;
        background-position: center;
        margin-bottom: 30px;
        margin-top: 30px;
    }

    /* Posities van de tekstlabels */
    .cb-label {
        position: absolute;
        right: 20px;
        bottom: 10px;
        width:auto;
        font-size: 12px;
    }

    .lb-label {
        position: absolute;
        top: 5px;
        left: 140px;
        font-size: 14px;
    }

    .totale-maat {
        position: absolute;
        top: -5px;
        left: 50%;
        transform: translateX(-50%);
        font-size: 14px;
    }

    /* Algemene tekststijl */
    .panel-banner strong {
        font-weight: bold;
    }

</style>
</body>
</html>
