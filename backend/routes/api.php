<?php

use App\Http\Controllers\InventoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
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

Route::middleware('api')->group(function () {
    // order
    Route::get('orders',[OrderController::class,'index']);
    Route::post('orders',[OrderController::class,'store']);
    Route::get('orders/test',[OrderController::class, 'testPerforment']);
    //product
    Route::post('products', [ProductController::class,'store']);
    Route::get('products',[ProductController::class, 'index']);
   

    //inventory
    Route::post('inventories', [InventoryController::class,'index']);
   
});