<?php

use App\Http\Controllers\invokes\CityServer;
use App\Http\Controllers\invokes\FormServer;
use App\Http\Controllers\invokes\TypedocumentUserServer;
use App\Http\Utils\googleUserToken;
use App\Models\Company;
use App\Models\EmployeePlanment;
use App\Models\Field;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

include __DIR__ . '/custom/auth.php';
include __DIR__ . '/custom/form.php';
include __DIR__ . '/custom/extraContent.php';
include __DIR__ . '/custom/export.php';
include __DIR__ . '/custom/googleService.php';
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

Route::get('/test', function (Request $request) {
    $filter = ['value' => 'Juan'];
    $data = EmployeePlanment::with(['charges:id,name', 'employee' => function ($query) {
        $query->with('third:id,names,surnames,identification,type_document');
        $query->select('employees.id', 'hire_date', 'end_date_contract', 'type_contract', 'employees.third_id');
    }, 'paymentMethod:id,name', 'planment' => function ($query) {
        $query->with(['invoice' => function ($query) {
            $query->with(['client' => function ($query) {
                $query->with('third:id,names,surnames');
                $query->select('clients.id', 'clients.third_id');
            }]);
            $query->select('invoices.id', 'invoices.client_id');
        }]);
        $query->select('planments.id', 'planments.invoice_id');
    }]);
    $invoice = $data->whereHas('employee', function ($query) use ($filter) {
        $query->whereHas('third', function ($query) use ($filter) {

            $query->whereRaw("UPPER(CONCAT(IFNULL(thirds.surnames,''),IFNULL(thirds.names,''),IFNULL(thirds.business_name,''),IFNULL(thirds.identification,''))) LIKE ?", ['%' . strtoupper($filter['value']) . '%']);
        });
    })->get();
    return $invoice;
});
