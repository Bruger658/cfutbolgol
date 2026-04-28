<?php

use App\Http\Controllers\GalleryItemController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Settings;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('index');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Route::get('/', function () {    
//     return view('index');
// })->name('index');    

Route::middleware(['auth'])->group(function () {       

   
    Route::get('settings/profile', [Settings\ProfileController::class, 'edit'])->name('settings.profile.edit');
    
    Route::put('settings/profile', [Settings\ProfileController::class, 'update'])->name('settings.profile.update');
    Route::delete('settings/profile', [Settings\ProfileController::class, 'destroy'])->name('settings.profile.destroy');
    Route::get('settings/password', [Settings\PasswordController::class, 'edit'])->name('settings.password.edit');
    Route::put('settings/password', [Settings\PasswordController::class, 'update'])->name('settings.password.update');
    Route::get('settings/appearance', [Settings\AppearanceController::class, 'edit'])->name('settings.appearance.edit');
    Route::put('settings/appearance', [Settings\AppearanceController::class, 'update'])->name('settings.appearance.update');

    Route::resource('gallery-items', GalleryItemController::class)->except(['show']);

    // Route::get('gallery-items', [GalleryItemController::class, 'index'])->name('gallery-items.index');
    // Route::get('gallery-items/create', [GalleryItemController::class, 'create'])->name('gallery-items.create');
    // Route::post('gallery-items', [GalleryItemController::class, 'store'])->name('gallery-items.store');
 
});


require __DIR__.'/auth.php';
