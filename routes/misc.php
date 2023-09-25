<?php


use App\Http\Controllers\Misc\AjaxController;
use Illuminate\Support\Facades\Route;

Route::prefix('ajax')->group(function(){
    Route::get('categories', [AjaxController::class,'categories'])->name('misc.ajax.categories');
    Route::get('customers', [AjaxController::class,'customers'])->name('misc.ajax.customers');
    Route::get('suppliers', [AjaxController::class,'suppliers'])->name('misc.ajax.suppliers');
    Route::get('products', [AjaxController::class,'products'])->name('misc.ajax.products');
    Route::get('chart-of-accounts', [AjaxController::class,'chart_of_accounts'])->name('misc.ajax.chart_of_accounts');
    Route::get('general-accounts', [AjaxController::class,'general_accounts'])->name('misc.ajax.general_accounts');
});
