<?php

use App\Http\Controllers\PlaidController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::post('plaid/confirm', [PlaidController::class, 'confirm'])->name('plaid.confirm');
Route::post('stripe/create-customer', [PlaidController::class, 'confirm'])->name('stripe.create-customer');

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
