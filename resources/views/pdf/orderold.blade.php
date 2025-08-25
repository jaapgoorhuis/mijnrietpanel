<html lang="en">
<head>
    <title>Invoice</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>

<div class="px-2 py-8 max-w-xl mx-auto">
    <div class="flex items-center justify-between mb-8">
        <div class="flex items-center">
            <img src="https://rietpanel.nl/wp-content/uploads/2024/08/Logo-Rietpanel-PNG-1024x419.png" alt="Logo" class="h-14"/>
        </div>
        <div class="text-gray-700">
            <div class="font-bold text-xl mb-2 uppercase">Order</div>
            <div class="text-sm">Order datum: {{date("d-m-Y", strtotime($order->created_at))}}</div>
            <div class="text-sm">order #: {{$order->order_id}}</div>
        </div>
    </div>
    <div class="border-b-2 border-gray-300 pb-8 mb-8">
        <h2 class="text-2xl font-bold mb-4">Order informatie:</h2>
        <div class="text-gray-700 mb-2"><strong>Project naam:</strong> {{$order->project_naam}}</div>
        @if($order->project_adres)
            <div class="text-gray-700 mb-2"><strong>Project adres:</strong> {{$order->project_adres}}</div>
        @endif
        <div class="text-gray-700 mb-2"><strong>Bedrijfsnaam:</strong> Crewa</div>
        <div class="text-gray-700 mb-2"><strong>Aangemaakt door:</strong> {{$order->intaker}}</div>
    </div>
    <table class="w-full text-left mb-8">
        <thead>
        <tr>
            <th class="text-gray-700 font-bold text-[12px]">Rietkleur</th>
            <th class="text-gray-700 font-bold text-[12px]">Toepassing</th>
            <th class="text-gray-700 font-bold text-[12px]">Merk paneel</th>
            <th class="text-gray-700 font-bold text-[12px]">CB</th>
            <th class="text-gray-700 font-bold text-[12px]">LB</th>
            <th class="text-gray-700 font-bold text-[12px]">Kerndikte</th>
            <th class="text-gray-700 font-bold text-[12px]">Totale lengte</th>
            <th class="text-gray-700 font-bold text-[12px]">m²</th>
            <th class="text-gray-700 font-bold text-[12px]">Aantal</th>
        </thead>
        <tbody>
        @foreach($orderLines as $orderLine)
        <tr>
            <td class="py-4 text-gray-700 text-[12px]">{{$orderLine->rietkleur}}</td>
            <td class="py-4 text-gray-700 text-[12px]">{{$orderLine->toepassing}}</td>
            <td class="py-4 text-gray-700 text-[12px]">{{$orderLine->merk_paneel}}</td>
            <td class="py-4 text-gray-700 text-[12px]">{{$orderLine->fillCb}}</td>
            <td class="py-4 text-gray-700 text-[12px]">{{$orderLine->fillLb}}</td>
            <td class="py-4 text-gray-700 text-[12px]">{{$orderLine->kerndikte}}</td>
            <td class="py-4 text-gray-700 text-[12px]">{{$orderLine->fillTotaleLengte}}</td>
            <td class="py-4 text-gray-700 text-[12px]">{{$orderLine->m2}} m²</td>
            <td class="py-4 text-gray-700 text-[12px]">{{$orderLine->aantal}}</td>
        </tr>
        @endforeach
        </tbody>
    </table>

    <div class="text-right">
        <?php $totalM2 = 0?>
        @foreach($order->orderLines as $orderLine)
                <?php $totalM2 += $orderLine->m2;?>
        @endforeach


        Totaal vierkante meters: <strong>{{$totalM2}} m²</strong>
    </div>
</div>

</body>
</html>
