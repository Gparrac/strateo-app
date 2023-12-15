<?php

use App\Http\Controllers\ExtraContent\CityServer;
use App\Http\Controllers\ExtraContent\FormServer;
use App\Http\Controllers\ExtraContent\PermissionServer;
use App\Http\Controllers\ExtraContent\TypedocumentUserServer;
use Illuminate\Support\Facades\Route;

Route::middleware('validate.name.invoke')->get('/cities', CityServer::class);
Route::get('/type-document-user', TypedocumentUserServer::class);
Route::get('/forms', FormServer::class);
Route::get('/permissions', PermissionServer::class);
