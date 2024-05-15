<?php

use App\Http\Controllers\invokes\CityServer;
use App\Http\Controllers\invokes\FormServer;
use App\Http\Controllers\invokes\TypedocumentUserServer;
use App\Http\Utils\googleUserToken;
use App\Models\Company;
use App\Models\EmployeePlanment;
use App\Models\Field;
use App\Models\Invoice;
use App\Models\Office;
use App\Models\User;
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
    Log::info(config('app.google_client_id'));
    Log::info(config('app.google_client_secret'));
    Log::info(config('app.google_redirect_url'));
    Log::info(config('app.front_redirect_url'));
    return env('DB_USERNAME');

});
