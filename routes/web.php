<?php

use App\Http\Controllers\ProfileController;
use App\Models\Order;
use App\Models\Supliers;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Route;
use STS\ZipStream\Facades\Zip;

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
    Route::get('orders/edit/{id}', \App\Livewire\Orders\EditOrders::class);
    Route::get('orders/remove/{id}', \App\Livewire\Orders\RemoveOrders::class);
    Route::get('orders/upload', \App\Livewire\Orders\UploadOrders::class);

    Route::get('offertes', \App\Livewire\Offertes\Offertes::class)->name('orders');
    Route::get('offertes/create', \App\Livewire\Offertes\CreateOffertes::class);
    Route::get('offertes/edit/{id}', \App\Livewire\Offertes\EditOffertes::class);

    Route::get('regulations', \App\Livewire\Regulations\Regulations::class)->name('regulations');
    Route::get('regulations/upload', \App\Livewire\Regulations\UploadRegulations::class);

    Route::get('details', \App\Livewire\Details\Details::class)->name('details');
    Route::get('details/upload', \App\Livewire\Details\UploadDetails::class);

    Route::get('marketing', \App\Livewire\Marketing\Marketing::class)->name('marketing');
    Route::get('marketing/upload', \App\Livewire\Marketing\UploadMarketing::class);

    Route::get('documentation', \App\Livewire\Documentation\Documentation::class)->name('documentation');
    Route::get('documentation/upload', \App\Livewire\Documentation\UploadDocumentation::class);

    Route::get('pricelist', \App\Livewire\Pricelist\Pricelist::class)->name('pricelist');
    Route::get('pricelist/upload', \App\Livewire\Pricelist\UploadPricelist::class);
    Route::get('/companys/{slug}/users/edit/{id}', \App\Livewire\Companys\EditCompanyUsers::class);
    Route::get('/companys/{id}/users', \App\Livewire\Companys\CompanyUsers::class);
    Route::get('/companys/pricerules', \App\Livewire\Companys\PriceRules\PriceRules::class)->name('companys/pricerules');
    Route::get('/companys/pricerules/create', \App\Livewire\Companys\PriceRules\CreatePriceRules::class);
    Route::get('/companys/pricerules/edit/{id}', \App\Livewire\Companys\PriceRules\EditPriceRules::class);
    Route::get('/companys/pricerules/remove/{id}', \App\Livewire\Companys\PriceRules\RemovePriceRules::class);


    Route::get('/mycompany', \App\Livewire\MyCompany\MyCompany::class)->name('mycompany');
    Route::get('/company/{slug}/pricerules/edit/{id}', \App\Livewire\Companys\PriceRules\EditCompanyPriceRules::class);


    Route::get('/companys', \App\Livewire\Companys\Companys::class)->name('companys');
    Route::get('/companys/create', \App\Livewire\Companys\CreateCompanys::class);
    Route::get('/companys/edit/{id}', \App\Livewire\Companys\EditCompanys::class);
    Route::get('/companys/remove/{id}', \App\Livewire\Companys\RemoveCompanys::class);

    Route::get('/accountrequests', \App\Livewire\AccountRequests\AccountRequests::class)->name('accountrequests');
    Route::get('/accountrequests/edit/{id}', \App\Livewire\AccountRequests\EditAccountRequests::class);

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

    Route::get('/download-zaaglijst/{id}', function($id) {
        $order = Order::where('id', $id)->first();

        Pdf::loadView('pdf.zaaglijst',['order' => $order])->save(public_path('/storage/zaaglijst/zaaglijst-'.$order->order_id.'.pdf'));

        $url = public_path('storage/zaaglijst/zaaglijst-'.$order->order_id.'.pdf');

        return response()->file($url);
    });

    Route::get('/download-pakketlijst/{id}', function($id) {
        $order = Order::where('id', $id)->first();

        Pdf::loadView('pdf.pakketlijst',['order' => $order])->save(public_path('/storage/pakketlijst/pakketlijst-'.$order->order_id.'.pdf'));

        $url = public_path('storage/pakketlijst/pakketlijst-'.$order->order_id.'.pdf');

        return response()->file($url);
    });

    Route::get('/download-orderlist/{id}', function($id) {
        $order = Order::where('id', $id)->first();
        $leverancier = Supliers::where('name', $order->merk_paneel)->first();
        Pdf::loadView('pdf.zaaglijst',['order' => $order,'leverancier'=> $leverancier])->save(public_path('/storage/orderlijst/order-'.$order->order_id.'.pdf'));

        $url = public_path('storage/orderlijst/order-'.$order->order_id.'.pdf');

        return response()->file($url);
    });

    Route::get('/download-bulk-zip', [\App\Http\Controllers\ZipDownloadController::class, 'download'])->name('download.bulk.zip');
});


require __DIR__.'/auth.php';
