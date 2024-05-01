<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\GoogleUser;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirect(){
        if (!config()->has("services.google")) {
            return $this->sendFailedResponse("google is not currently supported");
        }

        try {
            $redirectUrl = Socialite::driver('google')
            ->scopes(['https://www.googleapis.com/auth/calendar.events'])
            ->with(['access_type' => 'offline'])
            ->stateless()
            ->redirect()->getTargetUrl();
            return response()->json(['message'=> 'success','data' => $redirectUrl]);

        } catch (Exception $e) {
            Log::error('unknown error GoogleAuth@redirect: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'create ug'], 500);
        }
    }

    public function callback(){
        DB::beginTransaction();
        try {
        $user = Socialite::driver('google')->stateless()->user() ;
        $company = Company::first();
        if($company->googleUser){
            $company->googleUser->update([
                'name' => $user->getName(),
                'email' => $user->getEmail(),
                'id_account' => $user['id'],
                'refresh_token' => $user->refreshToken,
                'access_token' => $user->token,
                'time_expire' => time() + $user->expiresIn
            ]);
        }else{
           $googleUser =  GoogleUser::create([
                'name' => $user->getName(),
                'email' => $user->getEmail(),
                'id_account' => $user['id'],
                'refresh_token' => $user->refreshToken,
                'access_token' => $user->token,
                'time_expire' => time() + $user->expiresIn
            ]);
            $company->update([
                'google_user_id' => $googleUser->id
            ]);
        }
        DB::commit();

        return redirect('http://localhost:3000/enterprises');

     }catch (Exception $ex) {
            // In case of error, roll back the transaction
            DB::rollback();
            Log::error('unknown error GoogleAuth@callback: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'create ug'], 500);
        }
    }
}
