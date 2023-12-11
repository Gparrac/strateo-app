<?php

use App\Http\Controllers\invokes\CityServer;
use App\Http\Controllers\invokes\TypedocumentUserServer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

include __DIR__.'/custom/auth.php';
include __DIR__.'/custom/form.php';
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::middleware('validateNameInvoke')->get('/cities', CityServer::class);
Route::get('/type-document-user', TypedocumentUserServer::class);
