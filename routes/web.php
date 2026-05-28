<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\FixtureController;
use App\Http\Controllers\GalleryItemController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\ProductCheckoutController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PublicationController;
use App\Http\Controllers\Settings;
use App\Http\Controllers\TiendaController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('index');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('/tienda', [TiendaController::class, 'index'])->name('tienda');
Route::get('products/{product}/checkout/prepare', [ProductCheckoutController::class, 'prepare'])->name('products.checkout.prepare');
Route::post('products/{product}/checkout', [ProductCheckoutController::class, 'store'])->name('products.checkout.store');
Route::get('products/checkout/{order}', [ProductCheckoutController::class, 'show'])->name('products.checkout.show');
Route::get('products/checkout/{order}/success', [ProductCheckoutController::class, 'success'])->name('products.checkout.success');
Route::get('products/checkout/{order}/failure', [ProductCheckoutController::class, 'failure'])->name('products.checkout.failure');
Route::get('products/checkout/{order}/pending', [ProductCheckoutController::class, 'pending'])->name('products.checkout.pending');
Route::post('mercado-pago/webhook', [ProductCheckoutController::class, 'webhook'])->name('products.checkout.webhook');  

     

Route::middleware(['auth'])->group(function () {   
    Route::get('settings/profile', [Settings\ProfileController::class, 'edit'])->name('settings.profile.edit');
    Route::put('settings/profile', [Settings\ProfileController::class, 'update'])->name('settings.profile.update');
    Route::delete('settings/profile', [Settings\ProfileController::class, 'destroy'])->name('settings.profile.destroy');
    Route::get('settings/password', [Settings\PasswordController::class, 'edit'])->name('settings.password.edit');
    Route::put('settings/password', [Settings\PasswordController::class, 'update'])->name('settings.password.update');
    Route::get('settings/appearance', [Settings\AppearanceController::class, 'edit'])->name('settings.appearance.edit');
    Route::put('settings/appearance', [Settings\AppearanceController::class, 'update'])->name('settings.appearance.update');

    Route::resource('gallery-items', GalleryItemController::class)->except(['show']);

    

    
    
    // Route::get('noticias', [PublicationController::class, 'noticias'])->name('noticias');
    
    Route::resource('publications', PublicationController::class)->except(['show']);
    Route::get('noticias', [PublicationController::class, 'index'])->name('noticias.index');
    // Route::get('noticias', [PublicationController::class, 'index'])->name('noticias.noticias');

    Route::get('/tienda', [ProductController::class, 'index'])->name('products.index');
    
    Route::resource('fixtures', FixtureController::class)->except(['show']);
    Route::resource('events', EventController::class)->except(['show']);
    Route::patch('events/{event}/toggle', [EventController::class, 'toggle'])->name('events.toggle');
    Route::get('members-export/excel', [MemberController::class, 'exportExcel'])->name('members.export.excel');
    Route::get('members-export/pdf', [MemberController::class, 'exportPdf'])->name('members.export.pdf');
    Route::resource('members', MemberController::class)->except(['show']);
    Route::get('fixture', [FixtureController::class, 'index'])->name('fixture');
    Route::resource('products', ProductController::class)->except(['show']);
    // Route::get('products/{product}/checkout/prepare', [ProductCheckoutController::class, 'prepare'])->name('products.checkout.prepare');
    // Route::post('products/{product}/checkout', [ProductCheckoutController::class, 'store'])->name('products.checkout.store');
    // Route::get('products/checkout/{order}', [ProductCheckoutController::class, 'show'])->name('products.checkout.show');
});


require __DIR__.'/auth.php';
