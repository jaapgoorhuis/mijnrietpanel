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
                <div>Order #: </div>
                <div>Order datum:  </div><br/><br/>
                <div><h4>Order informatie:</h4></div><br/>
                <div>Project naam:</div>

                <div>Bedrijfsnaam: </div>
                <div>Aangemaakt door: </div>

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
            <th>CB</th>
            <th>LB</th>
            <th>Kerndikte</th>
            <th>Lengte</th>
            <th>m²</th>
            <th>Aantal</th>
            <th>Prijs</th>
        </tr>

            <tr class="items">
                <td>
                  test
                </td>
                <td>
                    test
                </td>
                <td>
                    test
                </td>
                <td>
                    test
                </td>
                <td>
                    test
                </td>
                <td>
                    test
                </td>
                <td>
                    test
                </td>
                <td>
                    test
                </td>
                <td>
                    test
                </td>
                <td>
                    test
                </td>
            </tr>

    </table>
</div>


<div class="totals-row">
    <div style="position:relative">
    <table class="total-table">
        <tr>
            <th style="text-align: left; line-height: 35px; border-bottom:1px solid black">Totaal m²:</th>
            <th style="line-height: 35px; text-align: left; border-bottom:1px solid black">12 m²</th>
        </tr>
        <br/>
        <tr>
            <th style="text-align: left; border-bottom:1px solid black">Subtotaal:</th>
            <th style="text-align: left; border-bottom:1px solid black">€ 12,-</th>
        </tr>
        <br/>
        <tr>
            <th style="text-align: left">21% BTW:</th>
            <th style="text-align: left">€ 12,-</th>
        </tr>
        <tr>
            <th style="text-align: left">Transportkosten:</th>
            <th style="text-align: left">€ 12,-</th>
        </tr>

        <tr style="padding:20px;">
            <th style="text-align: left; border-bottom:1px solid black; padding-right: 20px; margin-right: 20px;">Lengte toeslag zaaglengte (minimaal 2500mm):</th>
            <th style="text-align: left; border-bottom:1px solid black">€ 12,-</th>
        </tr>
        <br/>
        <tr>
            <th style="text-align: left;">Totaal incl 21% BTW:</th>
            <th style="text-align: left;">€ 12,-</th>
        </tr>
    </table>
    </div>
</div>

<style>
    h4 {
        margin: 0;
    }

    .totals-row {
        position: absolute;
        bottom: 50px;
        right: 0;
    }
    table.total-table {
        width:100%;

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
        font-weight: normal;
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
