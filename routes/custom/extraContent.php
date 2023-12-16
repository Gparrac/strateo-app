<?php

use App\Http\Controllers\ExtraContent\CityServer;
use App\Http\Controllers\ExtraContent\FormsServer;
use App\Http\Controllers\ExtraContent\PermissionServer;
use App\Http\Controllers\ExtraContent\TypedocumentUserServer;
use Illuminate\Support\Facades\Route;

Route::middleware('validate.name.invoke')->get('/cities', CityServer::class);
Route::get('/type-document-user', TypedocumentUserServer::class);
Route::get('/forms', FormsServer::class);
Route::get('/permissions', PermissionServer::class);
