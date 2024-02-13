<?php

use App\Http\Controllers\ExtraContent\CheckEnterpriseCreation;
use App\Http\Controllers\ExtraContent\CiiuServer;
use App\Http\Controllers\ExtraContent\CityServer;
use App\Http\Controllers\ExtraContent\FormsServer;
use App\Http\Controllers\ExtraContent\PermissionServer;
use App\Http\Controllers\ExtraContent\typeContractEmployee;
use App\Http\Controllers\ExtraContent\TypedocumentUserServer;
use App\Http\Controllers\ExtraContent\TypeFieldServer;
use App\Http\Controllers\ExtraContent\TypeInventoryTrade;
use App\Http\Controllers\ExtraContent\TypeProduct;
use GuzzleHttp\Middleware;
use Illuminate\Support\Facades\Route;
Route::middleware('validate.name.invoke')->get('/cities', CityServer::class);
Route::group(['middleware' => ['auth:api']], function() {
    Route::get('/check-enterprise', CheckEnterpriseCreation::class)->Middleware('role.user');
    Route::get('/forms', FormsServer::class);
});
Route::get('/ciiu-codes', CiiuServer::class)->middleware('validate.name.invoke');
Route::get('/permissions', PermissionServer::class);
Route::get('/type-document-user', TypedocumentUserServer::class);
Route::get('/type-fields', TypeFieldServer::class);
Route::get('/type-products', TypeProduct::class);
Route::get('/type-inventory-trades', TypeInventoryTrade::class);
Route::get('/type-contract-employee', typeContractEmployee::class);
