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
            <td rowspan="4" class="logo">
                <img src="{{ public_path("storage/images/rietpanel-R.png")}}" alt="" style="width: 100px;"/>
            </td>
        </tr>
        <tr>
            <td class="label">Klantnaam:</td>
            <td>{{$order->klantnaam}}</td>
        </tr>
        <tr>
            <td class="label">Plaats:</td>
            <td>{{$order->aflever_plaats}}</td>
        </tr>

        <tr>
            <td class="label">Project / ref:</td>
            <td>{{$order->referentie}}</td>
        </tr>
    </table>
    <table>
        <tr class="head">
            <td>Order regel</td>
            <td>Aantal</td>
            <td>Lengte</td>
            <td>CB</td>
            <td>LB</td>
        </tr>
        @foreach($order->orderLines as $key => $orderLines)
            <tr>
                <td>{{$key+1}}</td>
                <td>{{$orderLines->aantal}} stuks</td>
                <td>{{$orderLines->fillTotaleLengte}} mm</td>
                <td>{{$orderLines->fillCb}} mm</td>
                <td>{{$orderLines->fillLb}} mm</td>
            </tr>
        @endforeach
    </table>
    <table>
        <tr>
            <td>

                <img src="{{ public_path("storage/images/rietpanel_logo.png")}}" alt="" style="width: 100px; padding:10px; margin-left:30px;"/>
        </tr>
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
        width: 150px;
    }

    .logo {
        text-align: center;
        width: 100px;
    }
</style>
</body>
</html>
