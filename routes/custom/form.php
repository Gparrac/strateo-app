<?php

use App\Http\Controllers\ExtraContent\CityServer;
use Illuminate\Support\Facades\Route;
// use App\Http\Middleware\CRUD\CompanyParameterizationMiddleware;
use App\Http\Controllers\CRUD\EnterpriseParameterization;
use App\Http\Controllers\CRUD\RoleParameterization;
use App\Http\Controllers\CRUD\OfficeParameterization;
use App\Http\Controllers\CRUD\ClientParameterization;
use App\Http\Controllers\CRUD\UserParameterization;
use App\Http\Controllers\CRUD\ServiceParameterization;
use App\Http\Controllers\CRUD\FieldParameterization;
use App\Http\Controllers\CRUD\SupplierParameterization;
use App\Http\Controllers\CRUD\WarehouseParameterization;
use App\Http\Controllers\CRUD\MeasureParameterization;
use App\Http\Controllers\CRUD\BrandParameterization;
use App\Http\Controllers\CRUD\CategoryParameterization;
use App\Http\Controllers\CRUD\EmployeeParameterization;
use App\Http\Controllers\CRUD\InventoryParameterization;
use App\Http\Controllers\CRUD\ProductParameterization;
use App\Http\Controllers\ExportContent\InventoryController;


Route::group(['middleware' => ['auth:api','role.user']], function() {
    Route::match(['get', 'post', 'put', 'delete'],'/enterprise-parameterization', EnterpriseParameterization::class)->middleware('enterprise.parameterization');
    Route::match(['get', 'post', 'put', 'delete'], '/office-parameterization', OfficeParameterization::class)->middleware('office.parameterization');
    Route::match(['get', 'post', 'put', 'delete'],'/client-parameterization', ClientParameterization::class)->middleware('client.parameterization');
    Route::match(['get', 'post', 'put', 'delete'],'/user-parameterization', UserParameterization::class)->middleware('user.parameterization');
    Route::match(['get', 'post', 'put', 'delete'],'/service-parameterization', ServiceParameterization::class)->middleware('service.parameterization');
    //Route::match(['get', 'post', 'put', 'delete'],'/supplier-parameterization', RoleParameterization::class)->middleware('supplier.parameterization');
    Route::match(['get', 'post', 'put', 'delete'],'/field-parameterization', FieldParameterization::class)->middleware('field.parameterization');
    Route::match(['get', 'post', 'put', 'delete'],'/supplier-parameterization', SupplierParameterization::class)->middleware('supplier.parameterization');
    Route::match(['get', 'post', 'put', 'delete'],'/role-parameterization', RoleParameterization::class)->middleware('role.parameterization');

    Route::match(['get', 'post', 'put', 'delete'],'/warehouse-parameterization', WarehouseParameterization::class)->middleware('warehouse.parameterization');
    Route::match(['get', 'post', 'put', 'delete'],'/measure-parameterization', MeasureParameterization::class)->middleware('measure.parameterization');
    Route::match(['get', 'post', 'put', 'delete'],'/brand-parameterization', BrandParameterization::class)->middleware('brand.parameterization');
    Route::match(['get', 'post', 'put', 'delete'],'/category-parameterization', CategoryParameterization::class)->middleware('category.parameterization');
    Route::match(['get', 'post', 'put', 'delete'],'/inventory-parameterization', InventoryParameterization::class)->middleware('inventory.parameterization');
    Route::match(['get', 'post', 'put', 'delete'],'/product-parameterization', ProductParameterization::class)->middleware('product.parameterization');
    Route::match(['get', 'post', 'put', 'delete'],'/employee-parameterization', EmployeeParameterization::class)->middleware('employee.parameterization');

    Route::prefix('export-data')->group(function () {
        Route::get('/inventory-trades', InventoryController::class);
        // ... más rutas de configuración
    });
});

