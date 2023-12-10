<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;

class AuthController extends Controller
{
    public function signup(Request $request)
    {
        try {
            
            $user = User::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'password' => bcrypt($request->password),
            ]);

            return response()->json($this->getFormatTokenResponse($user), 200);
        } catch (QueryException $ex) {
            Log::error('Query error AuthController@signup: ' . $ex->getMessage());
            return response()->json(['message' => 'signup q'], 500);
        } catch (\Exception $ex) {
            Log::error('unknown error AuthController@signup: ' . $ex->getMessage());
            return response()->json(['message' => 'signup u'], 500);
        }
    }

    public function login(Request $request)
    {
        try {
            $user = $request->user;
            
            return response()->json($this->getFormatTokenResponse($user), 200);
        } catch (\Exception $ex) {
            Log::error('unknown error AuthController@login: ' . $ex->getMessage());
            return response()->json(['message' => 'login u'], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->token()->delete();

            return response()->json([
                'message' => 'Successfully logged out',
                'logout' => TRUE
            ]);
        } catch (\Exception $ex) {
            Log::error('unknown error AuthController@logout: ' . $ex->getMessage());
            return response()->json(['message' => 'logout u'], 500);
        }
    }

    private function getFormatTokenResponse($user){
        $user->tokens()->delete();
        $token = $user->createToken('strateo');
        $refresh_token = Crypt::encrypt($token->token->id);
        return [
            'token_type' => 'Bearer',
            'access_token' => $token->accessToken,
            'expires_at' => strtotime($token->token->expires_at),
        ];
    }
}