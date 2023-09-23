<?php


use App\Http\Controllers\Misc\AjaxController;
use Illuminate\Support\Facades\Route;

Route::prefix('ajax')->group(function(){
    Route::get('categories', [AjaxController::class,'categories'])->name('misc.ajax.categories');
    Route::get('customers', [AjaxController::class,'customers'])->name('misc.ajax.customers');
    Route::get('suppliers', [AjaxController::class,'suppliers'])->name('misc.ajax.suppliers');
});
