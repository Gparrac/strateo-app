<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CRUD;

Route::group(['middleware' => ['auth:api','role.user']], function() {
    Route::match(['get', 'post', 'put', 'delete'],'/enterprise-parameterization', CRUD\EnterpriseParameterization::class)->middleware('enterprise.parameterization');
    Route::match(['get', 'post', 'put', 'delete'], '/office-parameterization', CRUD\OfficeParameterization::class)->middleware('office.parameterization');
    Route::match(['get', 'post', 'put', 'delete'],'/client-parameterization', CRUD\ClientParameterization::class)->middleware('client.parameterization');
    Route::match(['get', 'post', 'put', 'delete'],'/user-parameterization', CRUD\UserParameterization::class)->middleware('user.parameterization');
    Route::match(['get', 'post', 'put', 'delete'],'/service-parameterization', CRUD\ServiceParameterization::class)->middleware('service.parameterization');
    //Route::match(['get', 'post', 'put', 'delete'],'/supplier-parameterization', RoleParameterization::class)->middleware('supplier.parameterization');
    Route::match(['get', 'post', 'put', 'delete'],'/field-parameterization', CRUD\FieldParameterization::class)->middleware('field.parameterization');
    Route::match(['get', 'post', 'put', 'delete'],'/supplier-parameterization', CRUD\SupplierParameterization::class)->middleware('supplier.parameterization');
    Route::match(['get', 'post', 'put', 'delete'],'/role-parameterization', CRUD\RoleParameterization::class)->middleware('role.parameterization');

    Route::match(['get', 'post', 'put', 'delete'],'/warehouse-parameterization', CRUD\WarehouseParameterization::class)->middleware('warehouse.parameterization');
    Route::match(['get', 'post', 'put', 'delete'],'/measure-parameterization', CRUD\MeasureParameterization::class)->middleware('measure.parameterization');
    Route::match(['get', 'post', 'put', 'delete'],'/brand-parameterization', CRUD\BrandParameterization::class)->middleware('brand.parameterization');
    Route::match(['get', 'post', 'put', 'delete'],'/category-parameterization', CRUD\CategoryParameterization::class)->middleware('category.parameterization');
    Route::match(['get', 'post', 'put', 'delete'],'/inventory-parameterization', CRUD\InventoryParameterization::class)->middleware('inventory.parameterization');
    Route::match(['get', 'post', 'put', 'delete'],'/product-parameterization', CRUD\ProductParameterization::class)->middleware('product.parameterization');
    Route::match(['get', 'post', 'put', 'delete'],'/employee-parameterization', CRUD\EmployeeParameterization::class)->middleware('employee.parameterization');
    Route::match(['get', 'post', 'put', 'delete'],'/invoice-parameterization', CRUD\InvoiceParameterization::class)->middleware('invoice.parameterization');
    Route::match(['get', 'post', 'put', 'delete'],'/tax-parameterization', CRUD\TaxParameterization::class)->middleware('tax.parameterization');
    Route::match(['get', 'post', 'put', 'delete'],'/tax-value-parameterization', CRUD\TaxValueParameterization::class)->middleware('tax.value.parameterization');
    Route::match(['get', 'post', 'put', 'delete'],'/purchase-order-parameterization', CRUD\PurchaseOrderParameterization::class)->middleware('purchase.order.parameterization');
    Route::match(['get', 'post', 'put', 'delete'],'/libretto-activity-parameterization', CRUD\LibrettoActivityParameterization::class)->middleware('libretto.activity.parameterization');
});

