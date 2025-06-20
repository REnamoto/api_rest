<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientsController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\ProductsStocksController;

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

// CRUD completo para ClientsController (GET, POST, PUT, DELETE, etc.)
Route::apiResource('clients', ClientsController::class);

// CRUD completo para ProductsController (GET, POST, PUT, DELETE, etc.)
Route::apiResource('products', ProductsController::class);

// CRUD completo para OrdersController (GET, POST, PUT, DELETE, etc.)
Route::apiResource('orders', OrdersController::class);

// CRUD completo para ProductsStocksController (GET, POST, PUT, DELETE, etc.)
Route::apiResource('products_stocks', ProductsStocksController::class);

// Documentação Swagger (frontend do Swagger UI)
Route::get('/api/documentation', function () {
    return view('l5-swagger::index');
});
