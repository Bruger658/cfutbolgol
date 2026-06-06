<?php

use App\Http\Controllers\EnrollmentRequestController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\FixtureController;
use App\Http\Controllers\GalleryItemController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\MemberFeePaymentController;
use App\Http\Controllers\ProductCheckoutController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PublicationController;
use App\Http\Controllers\Settings;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\TiendaController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('index');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('/tienda', [TiendaController::class, 'index'])->name('tienda');
Route::post('/inscripciones', [EnrollmentRequestController::class, 'store'])->name('enrollment-requests.store');
Route::get('/carrito', [CartController::class, 'show'])->name('cart.show');
Route::post('/carrito/{product}', [CartController::class, 'store'])->name('cart.store');
Route::patch('/carrito/{product}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/carrito/{product}', [CartController::class, 'destroy'])->name('cart.destroy');
Route::get('products/{product}/checkout/prepare', [ProductCheckoutController::class, 'prepare'])->name('products.checkout.prepare');
Route::get('/pagar-cuota', [MemberFeePaymentController::class, 'publicIndex'])->name('fees.public.index');
Route::post('/pagar-cuota/{member}/mercado-pago', [MemberFeePaymentController::class, 'mercadoPagoStore'])->name('fees.mercado-pago.store');
Route::get('/pagar-cuota/mercado-pago/{feePayment}/success', [MemberFeePaymentController::class, 'mercadoPagoSuccess'])->name('fees.mercado-pago.success');
Route::get('/pagar-cuota/mercado-pago/{feePayment}/failure', [MemberFeePaymentController::class, 'mercadoPagoFailure'])->name('fees.mercado-pago.failure');
Route::get('/pagar-cuota/mercado-pago/{feePayment}/pending', [MemberFeePaymentController::class, 'mercadoPagoPending'])->name('fees.mercado-pago.pending');
Route::post('/mercado-pago/cuotas/webhook', [MemberFeePaymentController::class, 'mercadoPagoWebhook'])->name('fees.mercado-pago.webhook');
Route::post('/pagar-cuota/{member}', [MemberFeePaymentController::class, 'publicStore'])->name('fees.public.store');
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

    
    Route::resource('publications', PublicationController::class)->except(['show']);
    Route::get('noticias', [PublicationController::class, 'index'])->name('noticias.index');
   
    Route::get('/tienda', [ProductController::class, 'index'])->name('products.index');
    
    Route::resource('fixtures', FixtureController::class)->except(['show']);
    Route::resource('events', EventController::class)->except(['show']);
    Route::resource('staff', StaffController::class)->except(['show']);
    Route::resource('enrollment-requests', EnrollmentRequestController::class)->only(['index', 'update', 'destroy']);
    Route::patch('events/{event}/toggle', [EventController::class, 'toggle'])->name('events.toggle');
    Route::get('members-export/excel', [MemberController::class, 'exportExcel'])->name('members.export.excel');
    Route::get('members-export/pdf', [MemberController::class, 'exportPdf'])->name('members.export.pdf');
    Route::get('members/fee-payments', [MemberFeePaymentController::class, 'index'])->name('members.fee-payments.index');
    Route::post('members/{member}/fee-payments', [MemberFeePaymentController::class, 'store'])->name('members.fee-payments.store');
    Route::resource('members', MemberController::class)->except(['show']);
    Route::get('fixture', [FixtureController::class, 'index'])->name('fixture');
    Route::resource('products', ProductController::class)->except(['show']);    
});


require __DIR__.'/auth.php';
