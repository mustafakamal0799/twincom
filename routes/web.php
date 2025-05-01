<?php

use App\Http\Controllers\CustomerCategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ItemController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/customer-category', [CustomerCategoryController::class, 'index']);
Route::get('/customer', [CustomerController::class, 'index'])->name('customers.index');

Route::get('/sync-customer', [CustomerController::class, 'syncCustomer']);

Route::get('/item', [ItemController::class, 'index']);
