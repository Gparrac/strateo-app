<?php
namespace App\Http\Utils;

use App\Models\Company;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class googleUserToken
{
    public function refreshAccessToken($refreshToken)
{
    $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
        'client_id' => config('services.google.client_id'),
        'client_secret' => config('services.google.client_secret'),
        'refresh_token' => $refreshToken,
        'grant_type' => 'refresh_token',
    ]);

    return $response->json();

}
    public function useRefreshedToken()
    {
        // Obtener el token de actualización de la base de datos
        $googleUser = Company::first()->googleUser;
        $refreshToken = $googleUser->refresh_token;
        if(time() >= $googleUser->time_expire){
            Log::info('passing');
            $response = $this->refreshAccessToken($refreshToken);
            $googleUser->update([
                'access_token' => $response['access_token'],
                'time_expire' => time() + $response['expires_in']
            ]);
        }

    }
}
