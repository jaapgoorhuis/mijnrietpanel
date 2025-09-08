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
                <div>Order #: test</div>
                <div>Order datum:   test</div><br/><br/>
                <div><h4>Order informatie:</h4></div><br/>
                <div>Project naam: test</div>



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


            <tr class="items">
                <td>
                    asd
                </td>
                <td>
                  asd
                </td>
                <td>
                   tre
                </td>
                <td>
                   23mm
                </td>
                <td>
                    23mm
                </td>
                <td>
                   23
                </td>
                <td>
                    23mm
                </td>
                <td>
                   23 m²
                </td>
                <td>
                  23 stuks
                </td>
            </tr>

    </table>
</div>

<div class="total">

    <table class="total-table">
        <tr>
            <th>Totaal m²</th>
            <th>12123</th>
        </tr>
        <tr>
            <th>Prijs:</th>
            <th>134</th>
        </tr>
    </table>
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
        padding:0.5rem;
    }
    table tr.items {
        background-color: #f9fafb;
    }
    table tr.items td {
        padding: 0.5rem 0.5rem 0.5rem 1rem;
    }

    table.total-table {
        width:150px;
        position: absolute;
        right: 20px;
    }
    .total {
        text-align: right;
        margin-top: 1rem;
        font-size: 0.875rem;
    }

</style>
</body>
</html>
