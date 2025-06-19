<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientsController;

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

// Documentação Swagger (frontend do Swagger UI)
Route::get('/api/documentation', function () {
    return view('l5-swagger::index');
});
