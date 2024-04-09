<?php
namespace App\Http\Utils;

use App\Models\Company;
use App\Models\GoogleUser;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleUserToken
{
    public static function refreshAccessToken($refreshToken)
{
    $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
        'client_id' => config('services.google.client_id'),
        'client_secret' => config('services.google.client_secret'),
        'refresh_token' => $refreshToken,
        'grant_type' => 'refresh_token',
    ]);

    return $response->json();

}
    public static function useRefreshedToken()
    {
        // Obtener el token de actualización de la base de datos
        $googleUser = GoogleUser::find(Company::first()->google_user_id);
        $refreshToken = $googleUser->refresh_token;
        $now = time();
        if($now >= $googleUser->time_expire){
            Log::info('passing');
            $response = self::refreshAccessToken($refreshToken);
            $googleUser->update([
                'access_token' => $response['access_token'],
                'time_expire' => $now + $response['expires_in'] + $now
            ]);
        }

    }
}
