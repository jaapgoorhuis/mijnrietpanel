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
            <img src="{{ public_path("storage/images/rietpanel_logo.png")}}" alt="" style="width: 200px;"/>
        </td>

    </tr>
</table>

<div class="margin-top">
    <table class="w-full">
        <tr>
            <td class="w-half">
                <div>Order #: {{$order->order_id}}</div>
                <div>Order datum:   {{date("d-m-Y", strtotime($order->created_at))}}</div><br/><br/>
                <div><h4>Order informatie:</h4></div><br/>
                <div>Project naam: {{$order->project_naam}}</div>
                @if($order->project_adres)
                    <div>Project adres: {{$order->project_adres}}</div>
                @endif
                <div>Bedrijfsnaam: {{$order->user->bedrijfsnaam}}</div>
                <div>Aangemaakt door: {{$order->intaker}}</div>

            </td>

        </tr>
    </table>
</div>

<div class="margin-top">
    <table class="products">
        <tr>
            <th>Rietkleur</th>
            <th>Toepassing</th>
            <th>Merk paneel</th>
            <th>CB</th>
            <th>LB</th>
            <th>Kerndikte</th>
            <th>Totale lengte</th>
            <th>m²</th>
            <th>Aantal</th>
        </tr>

        @foreach($orderLines as $orderLine)
            <tr class="items">
                <td>
                   {{$orderLine->rietkleur}}
                </td>
                <td>
                    {{$orderLine->toepassing}}
                </td>
                <td>
                    {{$orderLine->merk_paneel}}
                </td>
                <td>
                    {{$orderLine->fillCb}}
                </td>
                <td>
                    {{$orderLine->fillLb}}
                </td>
                <td>
                    {{$orderLine->kerndikte}}
                </td>
                <td>
                    {{$orderLine->fillTotaleLengte}}
                </td>
                <td>
                    {{$orderLine->m2}}
                </td>
                <td>
                    {{$orderLine->aantal}}
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

    Totale m²: {{$totalM2}}
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
        padding: 0.5rem;
    }
    table tr.items {
        background-color: #f9fafb;
    }
    table tr.items td {
        padding: 0.5rem;
    }
    .total {
        text-align: right;
        margin-top: 1rem;
        font-size: 0.875rem;
    }

</style>
</body>
</html>
