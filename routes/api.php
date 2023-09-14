<?php

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::any('webhook',[App\Http\Controllers\ApiController::class, 'webhook']);
Route::post('validation-token',[App\Http\Controllers\ApiController::class, 'validationToken']);
Route::post('send-message',[App\Http\Controllers\ApiController::class, 'sendMessage']);
Route::get('order-details/{orderId}',[App\Http\Controllers\ApiController::class, 'orderDetails']);

