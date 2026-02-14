<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Inkooporder</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .top-table td {
            vertical-align: top;
        }

        .top-table .left {
            width:70%
        }
        .top-table .right {
            width:30%;
        }

        .section-title {
            font-weight: bold;
            font-size: 18px;
            padding-bottom: 10px;
        }

        .orange {
            color: #C0A16E;
            font-weight: bold;
        }

        .order-details td {
            padding: 3px 0;
        }

        .order-details {
            width:auto;
            padding-right: 50px;
        }

        .logo {
            text-align: right;
            vertical-align: bottom;
        }

        .line {
            border-bottom: 2px solid black;
            margin: 10px 0;
        }

        .items-table th, .items-table td {
            border-bottom: 1px solid #ccc;
            text-align: left;
            padding: 4px;
        }

        .items-table th {
            color:#C0A16E;
        }

        .footer {
            margin-top: 30px;
            font-weight: bold;
        }

    </style>
</head>
<body>

<!-- Bovenste gedeelte met leverancier en afleveradres -->
<table>
    <tr>
        <td>

            <img src="{{ public_path("storage/images/rietpanel_logo.png")}}" alt="" style="width: 200px;"/>
        </td>
    </tr>
</table>
<br/>
<br/>

<table class="top-table">
    <tr>
        <td class="left">
            <div class="section-title"><span style="font-size:16px">Inkooporder</span></div>
            <span class="orange">Leverancier</span><br>
            {{$leverancier->suplier_name}}<br>
            t.a.v. Verkoop<br>
           {{$leverancier->suplier_straat}}<br>
            {{$leverancier->suplier_postcode}}<br>
            {{$leverancier->suplier_plaats}}<br>
            {{$leverancier->suplier_land}}<br><br>

            <table class="order-details">
                <tr>
                    <td>Ordernummer:</td><td>{{$order->order_id}}</td>
                </tr>
                <tr>
                    <td>Projectnaam:</td><td>{{$order->project_naam}}</td>
                </tr>
                <tr>
                    <td style="padding-right: 30px;">Aanmaakdatum:</td><td>{{$order->updated_at->format('d-m-Y')}}</td>
                </tr>
                <tr>
                    <td>Leverdatum:</td><td>{{$order->updated_at->addDays(14)->format('d-m-Y')}}</td>
                </tr>
                @if($order->orderRules)
                    @if($order->orderRules->show_orderlist)
                        <tr>
                            <td><strong>Opmerking(en):</strong></td>
                            <td>{{$order->orderRules->rule}}</td>
                        </tr>
                   @endif
                @endif
            </table>
        </td>
        <td class="right">
            <div style="height: 35px;"></div>
            <span class="orange">Afleveradres</span><br>
            Rietpanel<br>
            Nijverheidsweg 59<br>
            3771 ME Barneveld<br>
            Nederland<br><br>
        </td>
    </tr>
</table>

<div class="line"></div>

<!-- Tabel met posities -->
<table class="items-table">
    <thead>
    <tr>
        <th>Pos</th>
        <th>Aantal</th>
        <th>Lengte in MM</th>
        <th>Merk</th>
        <td>Leverdatum</td>
    </tr>
    </thead>
    <tbody>
    @foreach($order->orderLines->sortByDesc('fillTotaleLengte')->values() as $key => $orderLines)
    <tr>
        <td>
            {{$key+1}}
        </td>
        <td>
            {{$orderLines->aantal}}
        </td>
        <td>
            {{$orderLines->fillTotaleLengte}} mm
        </td>
        <td>
            {{$order->merk_paneel}}
        </td>
        <td>
            @if($order->delivery_date)
                {{$order->delivery_date}}
            @else
                Nog geen datum bekend
            @endif
        </td>
    </tr>
    @endforeach
    </tbody>
</table>

<p class="footer">
    Bij elke levering graag duidelijk vermelden welk ordernummer het is
</p>



</body>
</html>
