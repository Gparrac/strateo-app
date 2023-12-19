<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $user = $request->user;
            $dataUser = [
                'email' => $request['user']->name,
                'name' => $request['third']->email,
            ];
            return response()->json([
                'message' => 'login',
                'data' => $this->getFormatTokenResponse($user),
                'user' => $dataUser
            ]
            , 200);
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

    public function user(Request $request)
    {
        try {
            return response()->json([
                'message' => 'user',
                'data' => Auth::user(),
            ]);
        } catch (\Exception $ex) {
            Log::error('unknown error AuthController@user: ' . $ex->getMessage());
            return response()->json(['message' => 'user u'], 500);
        }
    }

    private function getFormatTokenResponse($user){
        $token = $user->createToken('strateo');
        $refresh_token = Crypt::encrypt($token->token->id);
        return [
            'token_type' => 'Bearer',
            'access_token' => $token->accessToken,
            'expires_at' => strtotime($token->token->expires_at),
        ];
    }
}
