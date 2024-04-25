<?php

use App\Http\Controllers\ExportContent\InventoryController;
use App\Http\Controllers\ExportContent\InvoicePDF;
use App\Http\Controllers\ExportContent\PlantmentCompanyPDF;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:api','role.user']], function() {

    Route::prefix('export-data')->group(function () {
        Route::get('/inventory-trades', InventoryController::class);
    });
});

Route::get('/invoice-pdf', InvoicePDF::class);
Route::get('/invoice-company-pdf', PlantmentCompanyPDF::class);


Route::get('/invoice-pdf-html', function () {
    return view('PDF.invoice');
});
