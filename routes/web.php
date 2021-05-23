<?php

use App\Http\Controllers\PlaidController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::view('/', 'welcome')->name('index');
Route::get('/link', [PlaidController::class, 'index'])->name('plaid');
Route::view('/wrong-customer', 'wrong_customer')->name('wrong_id');
