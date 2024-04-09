<?php

use App\Http\Controllers\invokes\CityServer;
use App\Http\Controllers\invokes\FormServer;
use App\Http\Controllers\invokes\TypedocumentUserServer;
use App\Http\Utils\googleUserToken;
use App\Models\Company;
use App\Models\Field;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;

include __DIR__.'/custom/auth.php';
include __DIR__.'/custom/form.php';
include __DIR__.'/custom/extraContent.php';
include __DIR__.'/custom/export.php';
include __DIR__.'/custom/googleService.php';
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

$googleUser = Company::first()->googleUser;
$googleMethods = new googleUserToken();
$googleMethods->useRefreshedToken();
// Suponiendo que $accessToken contiene el token de acceso válido
$client = new Client();
$response = $client->post('https://www.googleapis.com/calendar/v3/calendars/primary/events', [
    'headers' => [
        'Authorization' => 'Bearer ' . $googleUser->access_token,
    ],
    'json' => [
        'summary' => 'Evento prueba',
        'start' => [
            'dateTime' => '2024-04-08T10:00:00',
            'timeZone' => 'America/Los_Angeles',
        ],
        'end' => [
            'dateTime' => '2024-04-08T12:00:00',
            'timeZone' => 'America/Los_Angeles',
        ],
    ]
]);
$data = json_decode($response->getBody(), true);
return $data;
});
