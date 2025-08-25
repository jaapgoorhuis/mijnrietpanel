<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ProfileController;
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
    Route::get('orders/edit/{id}', \App\Livewire\Orders\EditOrders::class);
    Route::get('orders/upload', \App\Livewire\Orders\UploadOrders::class);

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

    Route::get('/users', \App\Livewire\Users\Users::class)->name('users');
    Route::get('user/edit/{id}', \App\Livewire\Users\EditUsers::class);

});


require __DIR__.'/auth.php';
