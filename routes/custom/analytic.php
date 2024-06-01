<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Metrics\MetricHandler;

Route::group(['middleware' => ['auth:api','role.user']], function() {
    Route::match(['get'],'/analytics/{type}', MetricHandler::class)->middleware('analytic');
    //Route::match(['get', 'post', 'put', 'delete'],'/supplier-parameterization', RoleParameterization::class)->middleware('supplier.parameterization');
});

