<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CRUD\CompanyParameterization;
use App\Http\Controllers\CRUD\UserParameterization;

Route::match(['get', 'post', 'put', 'delete'],'/company-parameterization', CompanyParameterization::class);
Route::match(['get', 'post', 'put', 'delete'],'/user-parameterization', UserParameterization::class);
