<?php


use App\Http\Controllers\Ajax\CustomerController;
use Illuminate\Support\Facades\Route;

Route::name('customer.')->prefix('customer')->group(function(){
    Route::get('list', [CustomerController::class,'index'])->name('list');
});
