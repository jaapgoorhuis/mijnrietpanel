<?php

use App\Http\Controllers\OrderPakketList;
use App\Http\Controllers\ProfileController;
use App\Models\Order;
use App\Models\OrderLines;
use App\Models\Supliers;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/dashboard', function () {

    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('orders', \App\Livewire\Orders\Orders::class)->name('orders');
    Route::get('orders/create', \App\Livewire\Orders\CreateOrders::class);
    Route::get('orders/confirm/{id}', \App\Livewire\Orders\EditOrders::class);
    Route::get('orders/remove/{id}', \App\Livewire\Orders\RemoveOrders::class);
    Route::get('orders/edit/{id}', \App\Livewire\Orders\ChangeOrder::class);
    Route::get('pakketten/{id}', [OrderPakketList::class, 'generate']);

    Route::get('offertes', \App\Livewire\Offertes\Offertes::class)->name('orders');
    Route::get('offertes/create', \App\Livewire\Offertes\CreateOffertes::class);
    Route::get('offertes/remove/{id}', \App\Livewire\Offertes\RemoveOffertes::class);
    Route::get('offertes/edit/{id}', \App\Livewire\Offertes\ChangeOfferte::class);
    Route::get('offertes/upload', \App\Livewire\Offertes\UploadOffertes::class);

    Route::get('regulations', \App\Livewire\Regulations\Regulations::class)->name('regulations');
    Route::get('regulations/upload', \App\Livewire\Regulations\UploadRegulations::class);

    Route::get('detail-maps', \App\Livewire\DetailFolder\DetailFolders::class)->name('detailFolders');
    Route::get('detail-maps/upload', \App\Livewire\DetailFolder\UploadDetailFolders::class)->name('detailFolders');
    Route::get('detail-maps/{id}/details', \App\Livewire\DetailFolder\Detail\Details::class)->name('detailFolders');
    Route::get('detail-maps/{id}/details/upload', \App\Livewire\DetailFolder\Detail\UploadDetails::class);

    Route::get('marketing-maps', \App\Livewire\MarketingFolder\MarketingFolder::class)->name('marketing');
    Route::get('marketing-maps/upload', \App\Livewire\MarketingFolder\UploadMarketingFolder::class)->name('marketing');
    Route::get('marketing-maps/{id}/marketing', \App\Livewire\MarketingFolder\Marketing\Marketing::class)->name('marketing');
    Route::get('marketing-maps/{id}/marketing/upload', \App\Livewire\MarketingFolder\Marketing\UploadMarketing::class);

    Route::get('documentation-maps', \App\Livewire\DocumentationFolder\DocumentationFolder::class)->name('documentation');
    Route::get('documentation-maps/upload', \App\Livewire\DocumentationFolder\UploadDocumentationFolder::class)->name('documentation');
    Route::get('documentation-maps/{id}/documentation', \App\Livewire\DocumentationFolder\Documentation\Documentation::class)->name('documentation');
    Route::get('documentation-maps/{id}/documentation/upload', \App\Livewire\DocumentationFolder\Documentation\UploadDocumentation::class);

    Route::get('pricelist-maps/pricelist', \App\Livewire\PricelistFolder\PricelistFolder::class)->name('pricelist');
    Route::get('pricelist-maps/upload', \App\Livewire\PricelistFolder\UploadPricelistFolder::class)->name('pricelist');
    Route::get('pricelist-maps/{id}/pricelist', \App\Livewire\PricelistFolder\Pricelist\Pricelist::class)->name('pricelist');
    Route::get('pricelist-maps/{id}/pricelist/upload', \App\Livewire\PricelistFolder\Pricelist\UploadPricelist::class);


    Route::get('/companys/{slug}/users/edit/{id}', \App\Livewire\Companys\EditCompanyUsers::class);
    Route::get('/companys/{id}/users', \App\Livewire\Companys\CompanyUsers::class);
    Route::get('/companys/pricerules', \App\Livewire\Companys\PriceRules\PriceRules::class)->name('companys/pricerules');
    Route::get('/companys/pricerules/create', \App\Livewire\Companys\PriceRules\CreatePriceRules::class);
    Route::get('/companys/pricerules/edit/{id}', \App\Livewire\Companys\PriceRules\EditPriceRules::class);
    Route::get('/companys/pricerules/remove/{id}', \App\Livewire\Companys\PriceRules\RemovePriceRules::class);

    Route::get('/companys/{slug}/subcontractors', \App\Livewire\Companys\Subcontractors\Subcontractor::class)->name('companys/subcontractors');
    Route::get('/companys/{slug}/subcontractors/create', \App\Livewire\Companys\Subcontractors\CreateSubcontractor::class);
    Route::get('/companys/{slug}/subcontractors/edit/{id}', \App\Livewire\Companys\Subcontractors\EditSubcontractor::class);
    Route::get('/companys/{slug}/subcontractors/remove/{id}', \App\Livewire\Companys\Subcontractors\RemoveSubcontractor::class);



    Route::get('/mycompany', \App\Livewire\MyCompany\MyCompany::class)->name('mycompany');
    Route::get('/company/{slug}/pricerules/edit/{id}', \App\Livewire\Companys\PriceRules\EditCompanyPriceRules::class);


    Route::get('/companys', \App\Livewire\Companys\Companys::class)->name('companys');
    Route::get('/companys/create', \App\Livewire\Companys\CreateCompanys::class);
    Route::get('/companys/edit/{id}', \App\Livewire\Companys\EditCompanys::class);
    Route::get('/companys/remove/{id}', \App\Livewire\Companys\RemoveCompanys::class);

    Route::get('/productPlanning', \App\Livewire\ProductPlanning\ProductPlanning::class)->name('productPlanning');
    Route::get('/api/orders', [\App\Livewire\ProductPlanning\ProductPlanning::class, 'getOrders']);

    Route::get('/product-planning/events', [\App\Livewire\ProductPlanning\ProductPlanning::class, 'events'])
        ->name('livewire.product-planning.events');


    Route::post('/api/orders/{id}/move', function ($id) {
        $order = Order::findOrFail($id);
        $order->planned_start = request('start');
        $order->save();

        return response()->json(['success' => true]);
    });

    Route::get('/accountrequests', \App\Livewire\AccountRequests\AccountRequests::class)->name('accountrequests');
    Route::get('/accountrequests/edit/{id}', \App\Livewire\AccountRequests\EditAccountRequests::class);
    Route::get('/accountrequests/remove/{id}', \App\Livewire\AccountRequests\RemoveAccountRequests::class);

    Route::get('/oldorder', \App\Livewire\Orders\OrderExample::class);

    Route::get('/surcharges', \App\Livewire\Surcharges\Surcharges::class)->name('surcharges');
    Route::get('/surcharges/create', \App\Livewire\Surcharges\CreateSurcharges::class);
    Route::get('/surcharges/edit/{id}', \App\Livewire\Surcharges\EditSurcharges::class);
    Route::get('/surcharges/remove/{id}', \App\Livewire\Surcharges\RemoveSurcharges::class);

    Route::get('/supliers', \App\Livewire\Supliers\Supliers::class)->name('supliers');
    Route::get('/supliers/create', \App\Livewire\Supliers\CreateSupliers::class);
    Route::get('/supliers/edit/{id}', \App\Livewire\Supliers\EditSupliers::class);
    Route::get('/supliers/remove/{id}', \App\Livewire\Supliers\RemoveSupliers::class);

    Route::get('/statisctics', \App\Livewire\Statistics\Statistics::class)->name('statistics');
    Route::get('/statistics/{id}', \App\Livewire\Statistics\ExpandedStatistics::class);

    Route::get('/zaaglijst', function () {

        return view('pdf.zaaglijsttest');
    });


    Route::get('/download-order/order-{id}', function($id) {
        $order = Order::where('order_id', $id)->firstOrFail();
        $orderLines = OrderLines::where('order_id', $order->id)->get();

        // Bepaal of kolommen überhaupt getoond moeten worden
        $showNokafschuining = $orderLines->where('nokafschuining', '>', 0)->count() > 0;
        $showVrijeRuimte = $orderLines->where('vrije_ruimte_2', '>', 0)->count() > 0;
        $showCb = $orderLines->where('fillCb', '>', 0)->count() > 0;
        $showLb = $orderLines->where('lb', '>', 0)->count() > 0;

        $company = $order->company; // Als je een relatie hebt
        $kerndikte = $order->kerndikte;

        Pdf::loadView('pdf.order', [
            'order' => $order,
            'orderLines' => $orderLines,
            'showNokafschuining' => $showNokafschuining,
            'showVrijeRuimte' => $showVrijeRuimte,
            'showLb' => $showLb,
            'showCb' => $showCb,
            'company' => $company,
            'kerndikte' => $kerndikte
        ])->save(public_path("/storage/orders/order-{$order->order_id}.pdf"));

        $url = public_path("storage/orders/order-{$order->order_id}.pdf");

        return response()->file($url);
    });

    Route::get('/download-offerte/offerte-{id}', function($id) {
        $offerte = \App\Models\Offerte::where('offerte_id', $id)->first();
        $offerteLines = \App\Models\OfferteLines::where('offerte_id', $offerte->id)->get();
        Pdf::loadView('pdf.offerte',['offerte' => $offerte,'offerteLines'=> $offerteLines])->save(public_path('/storage/offertes/offerte-'.$offerte->offerte_id.'.pdf'));

        $url = public_path('storage/offertes/offerte-'.$offerte->offerte_id.'.pdf');

        return response()->file($url);
    });


    Route::get('/download-fabriekslijst/fabrieklijst-{id}', function($id) {
        $order = Order::where('order_id', $id)->first();

        Pdf::loadView('pdf.fabriekslijst',['order' => $order])->save(public_path('/storage/fabriekslijst/fabriekslijst-'.$order->order_id.'.pdf'));

        $url = public_path('storage/fabriekslijst/fabriekslijst-'.$order->order_id.'.pdf');

        return response()->file($url);
    });

    Route::get('download-pakketlijst/pakketlijst-{id}', [OrderPakketList::class, 'generatePdf']);

//    Route::get('/download-pakketlijst/pakketlijst-{id}', function($id) {
//        $order = Order::where('order_id', $id)->first();
//
//        Pdf::loadView('pdf.pakketlijst',['order' => $order])->save(public_path('/storage/pakketlijst/pakketlijst-'.$order->order_id.'.pdf'));
//
//        $url = public_path('storage/pakketlijst/pakketlijst-'.$order->order_id.'.pdf');
//
//        return response()->file($url);
//    });

    Route::get('/download-orderlist/inkooporder-{id}', function($id) {
        $order = Order::where('order_id', $id)->first();
        $leverancier = Supliers::where('name', $order->merk_paneel)->first();
        Pdf::loadView('pdf.orderlijst',['order' => $order,'leverancier'=> $leverancier])->save(public_path('/storage/orderlijst/order-'.$order->order_id.'.pdf'));

        $url = public_path('storage/orderlijst/order-'.$order->order_id.'.pdf');

        return response()->file($url);
    });

    Route::get('/download-bulk-zip', [\App\Http\Controllers\ZipDownloadController::class, 'download'])->name('download.bulk.zip');
});


require __DIR__.'/auth.php';
