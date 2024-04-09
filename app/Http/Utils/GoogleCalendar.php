<?php

namespace App\Http\Utils;

use App\Models\Company;
use DateTime;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class GoogleCalendar
{
    public static function editEvent( $planment, $customer)
    {
        GoogleUserToken::useRefreshedToken();
        $googleUser = Company::first()->googleUser;
        $url = 'https://www.googleapis.com/calendar/v3/calendars/primary/events/';
        $client = new Client();
        $propsRequest = [
            'headers' => [
                'Authorization' => 'Bearer ' . $googleUser->access_token,
            ],
            'json' => [
                'summary' => $customer['fullname'],
                'start' => [
                    'dateTime' => DateTime::createFromFormat('Y-m-d H:i:s', $planment['start_date'])->format('Y-m-d\TH:i:s'),
                    'timeZone' => 'America/Bogota',
                ],
                'end' => [
                    'dateTime' => DateTime::createFromFormat('Y-m-d H:i:s', $planment['end_date'])->format('Y-m-d\TH:i:s'),
                    'timeZone' => 'America/Bogota',
                ],
            ]
            ];

            $response = $planment['event_google_id'] ? $client->patch($url . $planment['event_google_id'] , $propsRequest) : $client->post($url, $propsRequest);

        return json_decode($response->getBody(), true);
    }
    public static function deleteEvent( $eventId )
    {
        GoogleUserToken::useRefreshedToken();
        $googleUser = Company::first()->googleUser;
        $url = 'https://www.googleapis.com/calendar/v3/calendars/primary/events/' . $eventId;
        // Suponiendo que $accessToken contiene el token de acceso válido
        $client = new Client();
        $response = $client->delete($url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $googleUser->access_token,
            ]
        ]);
        if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
            // La solicitud fue exitosa, el evento se eliminó correctamente
            return true;
        } else {
            // La solicitud no fue exitosa, manejar el error aquí
            return false;
        }
    }


}
