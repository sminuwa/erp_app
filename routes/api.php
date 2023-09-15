<?php

use App\Http\Controllers\API\CategoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\BranchController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('product')->group(function () {
    Route::get('/list/{category?}', [ProductController::class, 'list'])->name('products.list');
    Route::get('/price/{product}/{branch}', [ProductController::class, 'price'])->name('products.price');
    Route::get('/category/list', [CategoryController::class, 'list'])->name('products.catogory.list');
    Route::get('/branch/list', [BranchController::class, 'list'])->name('branch.list');
});
